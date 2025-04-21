<?php
session_start();
require_once("../logins/dbcon.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    
    if (!$task_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid task ID']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Verify task belongs to user
    $checkSql = "SELECT id FROM tasks WHERE id = ? AND lawyer_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $task_id, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Task not found or not authorized']);
        exit;
    }
    $checkStmt->close();
    
    // Delete task
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete task']);
    }
    
    $stmt->close();
    $conn->close();
}
?>