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

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_awal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_akhir)) {
    die('Format tanggal tidak valid.');
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_tamu_$tanggal_awal" . "_sampai_$tanggal_akhir.xls");
header("Pragma: no-cache");
header("Expires: 0");

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

$sql = "SELECT * FROM tamu WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$no}</td>
        <td>{$row['tanggal']}</td>
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
