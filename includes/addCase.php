<?php
session_start();
require_once("../logins/dbcon.php");

// Add error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and fetch form inputs
    $caseNo = trim($_POST["case_number"]);
    $client = trim($_POST["client_name"]);
    $lawyerId = $_SESSION['user_id']; // Lawyer is the logged-in user
    $caseType = trim($_POST["type"]);
    $filingDate = $_POST["filing_date"];
    $nextHearing = $_POST["next_hearing"];
    $status = 'open'; 

    // Debug output
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $sql = "INSERT INTO cases (case_number, client_name, lawyer_id, type, filing_date, next_hearing, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssissss", $caseNo, $client, $lawyerId, $caseType, $filingDate, $nextHearing, $status);

    if ($stmt->execute()) {
        header("Location: ../lawyerPages/cases.php?added=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "This script only accepts POST requests from the form.";
}
$conn->close();
?>