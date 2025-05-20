<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "bukutamu_db"; // Pastikan kamu sudah buat database ini di phpMyAdmin

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
