<?php 
// Set proper content type for JSON response
header('Content-Type: application/json');

session_start();
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

require_once("../logins/dbcon.php");

// Debug connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if(isset($_POST['new_status'])) {
    $newStatus = $_POST['new_status'];
    $userID = $_SESSION['user_id'];

    if($newStatus !== 'active' && $newStatus !== 'inactive') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid status value']);
        exit();
    }

    // Debug query parameters
    error_log("Updating status for user ID: $userID to $newStatus");

    // Check if the column exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
    if ($checkColumn->num_rows == 0) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Status column does not exist in users table']);
        exit();
    }

    $updateQuery = "UPDATE users SET status = ? WHERE id = ? AND role = 'lawyer'";
    $stmt = $conn->prepare($updateQuery);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("si", $newStatus, $userID);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing status parameter']);
}
?>