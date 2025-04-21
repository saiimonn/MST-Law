<?php
session_start();
require_once("../logins/dbcon.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id']) || !isset($_POST['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = intval($_POST['user_id']);
$role = $_POST['role'];

$allowed_roles = ['client', 'lawyer', 'admin'];
if (!in_array($role, $allowed_roles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $role, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>