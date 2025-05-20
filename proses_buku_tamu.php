<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_tamu = trim($_POST['nama_tamu']);
    $alamat = trim($_POST['alamat']);
    $nama_tujuan = trim($_POST['nama_tujuan']);
    $acara = trim($_POST['acara']);

    if (empty($nama_tamu) || empty($alamat) || empty($nama_tujuan) || empty($acara)) {
        header("Location: index.php?status=error&message=" . urlencode("Semua kolom wajib diisi."));
        exit;
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: index.php?status=error&message=" . urlencode("Foto wajib diunggah."));
        exit;
    }

    $foto = $_FILES['foto'];
    $foto_size = $foto['size'];
    $foto_tmp = $foto['tmp_name'];
    $foto_name = $foto['name'];
    $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($foto_ext, $allowed_ext)) {
        header("Location: index.php?status=error&message=" . urlencode("Format foto tidak didukung."));
        exit;
    }

    if ($foto_size > 5 * 1024 * 1024) {
        header("Location: index.php?status=error&message=" . urlencode("Ukuran foto maksimal 5MB."));
        exit;
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $new_filename = uniqid('foto_', true) . '.' . $foto_ext;
    $upload_path = $upload_dir . $new_filename;

    if (!move_uploaded_file($foto_tmp, $upload_path)) {
        header("Location: index.php?status=error&message=" . urlencode("Gagal mengunggah foto."));
        exit;
    }

    $datetime = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO tamu (nama_tamu, alamat, nama_tujuan, acara, tanggal, foto) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        // Kalau prepare gagal
        if (file_exists($upload_path)) unlink($upload_path);
        header("Location: index.php?status=error&message=" . urlencode("Gagal menyiapkan query."));
        exit;
    }
    
    $stmt->bind_param("ssssss", $nama_tamu, $alamat, $nama_tujuan, $acara, $datetime, $new_filename);

    if ($stmt->execute()) {
        header("Location: index.php?status=success&message=" . urlencode("Data berhasil disimpan!"));
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
