<?php 
session_start();
include('../logins/dbcon.php');

if (isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];
    $user_id = $_SESSION['user_id'];

    // Allow admin to cancel any appointment
    if ($_SESSION['user_role'] == 'admin') {
        $query = "UPDATE appointments SET status = 'cancelled' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $appointment_id);
    } else {
        $query = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $appointment_id, $user_id);
    }

    if($stmt->execute()) {
        $_SESSION['status'] = "Appointment cancelled successfully.";
    } else {
        $_SESSION['status'] = "Failed to cancel the appointment.";
    }
} else {
    $_SESSION['status'] = "Invalid appointment ID.";
}

if($_SESSION['user_role'] == 'client') {
    header("Location: ../clientPages/clientHome.php");
    exit();
} elseif($_SESSION['user_role'] == 'lawyer') {
    header("Location: ../lawyerPages/lawyerHome.php");
    exit();
} else {
    header("Location: ../adminPages/adminHome.php");
    exit();
}
?>