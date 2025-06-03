<?php
include 'koneksi.php';

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$serviceAccount = json_decode(file_get_contents("service-account.json"), true);

$now = time();
$header = ['alg' => 'RS256', 'typ' => 'JWT'];
$claims = [
    'iss' => $serviceAccount['client_email'],
    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
    'aud' => 'https://oauth2.googleapis.com/token',
    'iat' => $now,
    'exp' => $now + 3600,
];

$jwtHeader = base64url_encode(json_encode($header));
$jwtClaims = base64url_encode(json_encode($claims));
$signatureInput = $jwtHeader . '.' . $jwtClaims;
openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'sha256WithRSAEncryption');
$jwt = $signatureInput . '.' . base64url_encode($signature);

$tokenRequest = [
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $jwt
];

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequest));
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);
if (!isset($tokenData['access_token'])) {
    die("Gagal mendapatkan access token: " . $response);
}
$accessToken = $tokenData['access_token'];

// Ambil semua token admin/operator dari DB
$tokens = [];
$result = $conn->query("SELECT token FROM fcm_tokens WHERE user_role IN ('admin', 'operator')");
while ($row = $result->fetch_assoc()) {
    $tokens[] = $row['token'];
}
$conn->close();

if (empty($tokens)) {
    die("Tidak ada token terdaftar.");
}

foreach ($tokens as $deviceToken) {
    $body = [
        "message" => [
            "token" => $deviceToken,
            "notification" => [
                "title" => "Tamu Baru",
                "body" => "Ada tamu baru yang mengisi buku tamu."
            ]
        ]
    ];

    $ch = curl_init("https://fcm.googleapis.com/v1/projects/buku-tamu-44225/messages:send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Log (optional)
    echo "Token: $deviceToken\nStatus: $httpCode\nResponse: $response\n\n";
}
?>
