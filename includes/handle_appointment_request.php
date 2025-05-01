<?php 
session_start();
require_once("../logins/dbcon.php");

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['appointment_id']) && isset($_GET['action'])) {
    $appointment_id = $_GET['appointment_id'];
    $action = $_GET['action'];
    $attorney_id = $_SESSION['user_id']; // This is the lawyer's ID

    // Debug output
    error_log("Processing appointment: " . $appointment_id);
    error_log("Action: " . $action);
    error_log("Attorney ID: " . $attorney_id);

    // Set the status based on action
    $status = ($action === 'confirmed') ? 'confirmed' : 'rejected';

    // Allow admin to approve/reject any appointment
    if ($_SESSION['user_role'] == 'admin') {
        $query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $appointment_id);
    } else {
        // Only allow lawyers to update their own appointments
        $query = "UPDATE appointments SET status = ? WHERE id = ? AND attorney_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $status, $appointment_id, $attorney_id);
    }

    if ($stmt->execute()) {
        error_log("Update successful. New status: " . $status);
        $_SESSION['status'] = "Appointment has been " . $status;
    } else {
        error_log("Update failed. Error: " . $stmt->error);
        $_SESSION['status'] = "Failed to update appointment status";
    }
} else {
    $_SESSION['status'] = "Invalid request parameters";
}

if($_SESSION['user_role'] == 'lawyer') {
    header("Location: ../lawyerPages/lawyerHome.php");
    exit();
} elseif($_SESSION['user_role'] == 'admin') {
    header("Location: ../adminPages/adminHome.php");
    exit();
}
?>
