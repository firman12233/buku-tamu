<?php
include 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = $data['token'] ?? $_POST['token'] ?? '';
$role = $data['role'] ?? $_POST['role'] ?? '';

if (!$token || !$role) {
    echo json_encode(['status' => 'error', 'message' => 'Token atau role kosong']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO fcm_tokens (token, user_role) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_role=VALUES(user_role)");
$stmt->bind_param("ss", $token, $role);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Token tersimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan token']);
}

$stmt->close();
$conn->close();
?>
