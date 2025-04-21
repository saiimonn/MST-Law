<?php 
session_start();
require_once 'db_connection.php';

try {
    $conn->beginTransaction();

    if(empty($_POST["client"]) || empty($_POST["date"]) ||
        empty($_POST["start_time"]) || empty($_POST["end_time"])) {
        throw new Exception("All fields are required!");
    }

    $client_id = filter_input(INPUT_POST, 'client', FILTER_SANITIZE_NUMBER_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_SPECIAL_CHARS);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_SPECIAL_CHARS);
    $attorney_id = filter_input(INPUT_POST, 'attorney_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);




    $currDate = date('Y-m-d');
    if($date < $currDate) {
        throw new Exception("Cannot schedule an appointment in the past!");
    }

    if($start_time >= $end_time) {
        throw new Exception("End time must be after start time!");
    }

    $conflictCheck = "SELECT COUNT(*) FROM appointments
                        WHERE attorney_id = ?
                        AND date = ?
                        AND status = 'confirmed'
                        AND ((start_time BETWEEN ? AND ?)
                        OR (end_time BETWEEN ? AND ?))";
    
    $stmt = $conn->prepare($conflictCheck);
    $stmt->execute([$attorney_id, $date, $start_time, $end_time, $start_time, $end_time]);

    if($stmt->fetchColumn() > 0) {
        throw new Exception("Time slot conflicts with existing appointment!");
    }

    // Remove phone from insert
    $insertSQL = "INSERT INTO appointments (user_id, attorney_id, date, start_time, end_time, status, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertSQL);
    $stmt->execute([$client_id, $attorney_id, $date, $start_time, $end_time, $status, $description]);

    $conn->commit();
    $_SESSION['success'] = "Appointment scheduled successfully!";
    header("Location: ../lawyerPages/scheduleAppointment.php");
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../lawyerPages/scheduleAppointment.php");
    exit();
}
?>