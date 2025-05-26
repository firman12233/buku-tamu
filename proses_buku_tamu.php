<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_tamu   = trim($_POST['nama_tamu']);
    $alamat      = trim($_POST['alamat']);
    $nomer_hp    = trim($_POST['nomer_hp']);
    $asal_instansi    = trim($_POST['asal_instansi']);
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
        // ✅ Sukses simpan → arahkan ke halaman terima kasih
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
