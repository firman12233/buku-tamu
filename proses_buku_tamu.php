<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_tamu   = trim($_POST['nama_tamu']);
    $alamat      = trim($_POST['alamat']);
    $nomer_hp    = trim($_POST['nomer_hp']);
    $asal_instansi = trim($_POST['asal_instansi']);
    $nama_tujuan = trim($_POST['nama_tujuan']);
    $keperluan   = trim($_POST['keperluan']);
    $tanggal     = date('Y-m-d H:i:s');

    // Validasi input
    if (empty($nama_tamu) || empty($alamat) || empty($nomer_hp) || empty($asal_instansi) || empty($nama_tujuan) || empty($keperluan)) {
        header("Location: index.php?status=error&message=" . urlencode("Semua kolom wajib diisi."));
        exit;
    }

    // Validasi foto
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: index.php?status=error&message=" . urlencode("Foto wajib diunggah."));
        exit;
    }

    $foto       = $_FILES['foto'];
    $foto_name  = $foto['name'];
    $foto_tmp   = $foto['tmp_name'];
    $foto_size  = $foto['size'];
    $foto_ext   = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($foto_ext, $allowed_ext)) {
        header("Location: index.php?status=error&message=" . urlencode("Format foto tidak didukung."));
        exit;
    }

    if ($foto_size > 5 * 1024 * 1024) {
        header("Location: index.php?status=error&message=" . urlencode("Ukuran foto maksimal 5MB."));
        exit;
    }

    // Upload foto
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $new_filename = uniqid('foto_', true) . '.' . $foto_ext;
    $upload_path  = $upload_dir . $new_filename;

    if (!move_uploaded_file($foto_tmp, $upload_path)) {
        header("Location: index.php?status=error&message=" . urlencode("Gagal mengunggah foto."));
        exit;
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO tamu (nama_tamu, alamat, nomer_hp, asal_instansi, nama_tujuan, keperluan, tanggal, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        if (file_exists($upload_path)) unlink($upload_path);
        header("Location: index.php?status=error&message=" . urlencode("Gagal menyiapkan query."));
        exit;
    }

    $stmt->bind_param("ssssssss", $nama_tamu, $alamat, $nomer_hp, $asal_instansi, $nama_tujuan, $keperluan, $tanggal, $new_filename);

    if ($stmt->execute()) {
        // Kirim notifikasi ke semua token admin/operator
        $result = $conn->query("SELECT token FROM fcm_tokens WHERE user_role IN ('admin', 'operator')");
        while ($row = $result->fetch_assoc()) {
            sendFCMNotification($row['token'], 'Tamu Baru', "Ada tamu baru bernama $nama_tamu.");
        }

        header("Location: terima_kasih.html");
        exit;
    } else {
        if (file_exists($upload_path)) unlink($upload_path);
        header("Location: index.php?status=error&message=" . urlencode("Gagal menyimpan data: " . $stmt->error));
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}

// =========================
// Fungsi Kirim Notifikasi
// =========================
function sendFCMNotification($token, $title, $body) {
    $serviceAccountPath = __DIR__ . '/service-account.json';

    $accessToken = getAccessToken($serviceAccountPath);
    if (!$accessToken) {
        error_log('Gagal mendapatkan access token Firebase');
        return;
    }

    $url = 'https://fcm.googleapis.com/v1/projects/buku-tamu-44225/messages:send';

    $message = [
        "message" => [
            "token" => $token,
            "notification" => [
                "title" => $title,
                "body" => $body
            ],
            "webpush" => [
                "fcm_options" => [
                    "link" => "http://localhost/buku-tamu/admin.php"
                ]
            ]
        ]
    ];

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("cURL Error: $err");
    } else {
        error_log("FCM Response: $response");
    }
}

function getAccessToken($serviceAccountPath) {
    $credentials = json_decode(file_get_contents($serviceAccountPath), true);
    if (!$credentials) return null;

    $now = time();

    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $claimSet = json_encode([
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $credentials['token_uri'],
        'iat' => $now,
        'exp' => $now + 3600
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlClaimSet = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claimSet));
    $unsignedJWT = $base64UrlHeader . '.' . $base64UrlClaimSet;

    $privateKey = openssl_pkey_get_private($credentials['private_key']);
    if (!$privateKey) return null;

    openssl_sign($unsignedJWT, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $unsignedJWT . '.' . $base64UrlSignature;

    $postFields = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

    $ch = curl_init($credentials['token_uri']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("cURL Error getAccessToken: $err");
        return null;
    }

    $resultData = json_decode($result, true);
    return $resultData['access_token'] ?? null;
}
