<?php
session_start();
require_once("../logins/dbcon.php");

header('Content-Type: application/json');
error_log("Attempting to send message");

try {
    if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
        throw new Exception('Missing required data');
    }

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    error_log("Sending message from $sender_id to $receiver_id: $message");

    // Insert message
    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
    if (!$stmt->execute()) {
        error_log("Failed to insert message: " . $stmt->error);
        throw new Exception($stmt->error);
    }

    $message_id = $stmt->insert_id;
    error_log("Message inserted successfully with ID: " . $message_id);

    echo json_encode([
        'status' => 'success',
        'message_id' => $message_id
    ]);

} catch (Exception $e) {
    error_log("Send Message Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
