<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

if (!isset($_GET['tanggal_awal']) || !isset($_GET['tanggal_akhir'])) {
    die('Parameter tanggal tidak lengkap.');
}

$tanggal_awal = $_GET['tanggal_awal'];
$tanggal_akhir = $_GET['tanggal_akhir'];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_awal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_akhir)) {
    die('Format tanggal tidak valid.');
}


$filename = "data_tamu_{$tanggal_awal}_sampai_{$tanggal_akhir}.csv";

header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");


$output = fopen('php://output', 'w');


fputcsv($output, ['No', 'Tanggal', 'Nama Tamu', 'Alamat', 'Nomor HP', 'Asal Instansi', 'Nama Tujuan', 'Keperluan']);

$sql = "SELECT * FROM tamu WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $no++,
        $row['tanggal'],
        $row['nama_tamu'],
        $row['alamat'],
        $row['nomer_hp'],
        $row['asal_instansi'],
        $row['nama_tujuan'],
        $row['keperluan']
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
exit;
?>
