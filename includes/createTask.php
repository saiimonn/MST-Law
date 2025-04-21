<?php 
session_start();
require_once("../logins/dbcon.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $details = trim($_POST["details"]);
    $deadline = isset($_POST["deadline"]) ? $_POST["deadline"] : null;
    $user_id = $_SESSION["user_id"];
    
    // For a lawyer's todo list, they are both the lawyer and creator
    $lawyer_id = $user_id;
    $creator_id = $user_id;

    if(empty($title)) {
        echo json_encode(["status" => "error", "message" => "Title must be provided."]);
        exit;
    }
    
    $sql = "INSERT INTO tasks (lawyer_id, title, details, deadline, created_by, status)
            VALUES (?, ?, ?, ?, ?, 'active')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $lawyer_id, $title, $details, $deadline, $creator_id);
    
    if ($stmt->execute()) {
        header("Location: ../lawyerPages/todo.php");
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create task: " . $conn->error]);
    }
    
    $stmt->close();
    $conn->close();
}
?>