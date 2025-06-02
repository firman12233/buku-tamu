<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// Pastikan parameter ada
if (!isset($_GET['tanggal_awal']) || !isset($_GET['tanggal_akhir'])) {
    die('Parameter tanggal tidak lengkap.');
}

// Ambil tanggal dari URL
$tanggal_awal = $_GET['tanggal_awal'];
$tanggal_akhir = $_GET['tanggal_akhir'];

// Validasi format tanggal (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_awal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_akhir)) {
    die('Format tanggal tidak valid.');
}

// Tambahkan waktu agar mencakup seluruh hari
$tanggal_awal_full = $tanggal_awal . ' 00:00:00';
$tanggal_akhir_full = $tanggal_akhir . ' 23:59:59';

// Nama file
$filename = "data_tamu_" . $tanggal_awal . "_sampai_" . $tanggal_akhir . ".xls";

// Header Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");
echo "\xEF\xBB\xBF"; // BOM untuk UTF-8 Excel

// Tabel header
echo "<table border='1'>";
echo "<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Nama Tamu</th>
    <th>Alamat</th>
    <th>Nomor HP</th>
    <th>Asal Instansi</th>
    <th>Nama Tujuan</th>
    <th>Keperluan</th>
</tr>";

// Query data tamu
$sql = "SELECT * FROM tamu WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $tanggal_awal_full, $tanggal_akhir_full);
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan data
$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$no}</td>
        <td>" . htmlspecialchars($row['tanggal']) . "</td>
        <td>" . htmlspecialchars($row['nama_tamu']) . "</td>
        <td>" . htmlspecialchars($row['alamat']) . "</td>
        <td>" . htmlspecialchars($row['nomer_hp']) . "</td>
        <td>" . htmlspecialchars($row['asal_instansi']) . "</td>
        <td>" . htmlspecialchars($row['nama_tujuan']) . "</td>
        <td>" . htmlspecialchars($row['keperluan']) . "</td>
    </tr>";
    $no++;
}

echo "</table>";

$stmt->close();
$conn->close();
?>
