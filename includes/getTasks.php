<?php
session_start();
require_once("../logins/dbcon.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, title, details, deadline, status FROM tasks 
        WHERE lawyer_id = ? 
        ORDER BY 
            CASE WHEN status = 'completed' THEN 1 ELSE 0 END,
            CASE WHEN deadline IS NULL THEN 1 ELSE 0 END,
            deadline ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode($tasks);

$stmt->close();
$conn->close();
?>