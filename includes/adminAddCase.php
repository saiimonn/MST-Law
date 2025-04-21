<?php
session_start();
require_once("../logins/dbcon.php");

// Add error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and fetch form inputs
    $caseNo = trim($_POST["caseNo"]);
    $client = trim($_POST["client"]);
    $lawyerId = intval($_POST["lawyer"]);
    $caseType = trim($_POST["caseType"]);
    $filingDate = $_POST["filingDate"];
    $nextHearing = $_POST["nextHearing"];
    $status = 'open'; 

    // Debug output
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // The error is in this SQL statement - the column 'client' doesn't exist
    // Let's modify it to use the correct column name, likely 'client_name' or similar
    $sql = "INSERT INTO cases (case_number, client_name, lawyer_id, type, filing_date, next_hearing, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Fix the parameter types to match your data
    $stmt->bind_param("ssissss", $caseNo, $client, $lawyerId, $caseType, $filingDate, $nextHearing, $status);

    if ($stmt->execute()) {
        // Update the redirect path to match your file structure
        header("Location: ../adminPages/schedules.php?added=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // If not a POST request
    echo "This script only accepts POST requests from the form.";
}
$conn->close();
?>