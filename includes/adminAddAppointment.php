<?php
session_start();
require_once("../logins/dbcon.php");

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and fetch input values
    $clientId   = trim($_POST["client"]);
    $lawyerId   = trim($_POST["lawyer"]);
    $appDate    = trim($_POST["appDate"]);
    $startTime  = trim($_POST["startTime"]);
    $endTime    = trim($_POST["endTime"]);
    $details    = trim($_POST["details"]);

    // Basic validation
    if (empty($clientId) || empty($lawyerId) || empty($appDate) || empty($startTime) || empty($endTime)) {
        die("Missing required fields.");
    }

    // Optionally, check for appointment conflicts here (not implemented)

    // Prepare and insert into the appointments table
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, attorney_id, date, start_time, end_time, description, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isssss", $clientId, $lawyerId, $appDate, $startTime, $endTime, $details);

    if ($stmt->execute()) {
        // Redirect or show success
        header("Location: ../adminPages/schedules.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Block direct access
    header("Location: ../adminPages/schedules.php");
    exit();
}