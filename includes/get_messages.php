<?php
session_start();
require_once("../logins/dbcon.php");

header('Content-Type: application/json');
error_log("Getting messages for user_id: " . $_SESSION['user_id'] . " and receiver_id: " . $_GET['receiver_id']);

try {
    if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
        throw new Exception('Missing required data');
    }

    $user_id = $_SESSION['user_id'];
    $receiver_id = $_GET['receiver_id'];

    // Simpler query without joins for testing
    $query = "SELECT * FROM messages 
              WHERE (sender_id = ? AND receiver_id = ?) 
              OR (sender_id = ? AND receiver_id = ?)
              ORDER BY timestamp ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
    
    if (!$stmt->execute()) {
        error_log("Query execution failed: " . $stmt->error);
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();
    $messages = [];
    
    while ($row = $result->fetch_assoc()) {
        error_log("Found message: " . print_r($row, true));
        $messages[] = $row;
    }

    error_log("Returning " . count($messages) . " messages");
    echo json_encode($messages);

} catch (Exception $e) {
    error_log("Get Messages Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
