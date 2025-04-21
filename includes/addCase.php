<?php
session_start();
require_once("../logins/dbcon.php");

header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php-error.log');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $required = ['case_number', 'client_name', 'type', 'filing_date', 'next_hearing'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
            exit;
        }
    }

    $caseNo = $conn->real_escape_string($_POST["case_number"]);
    $client = $conn->real_escape_string($_POST["client_name"]);
    $lawyerId = intval($_SESSION['user_id']);
    $caseType = $conn->real_escape_string($_POST["type"]);
    $filingDate = $conn->real_escape_string($_POST["filing_date"]);
    $nextHearing = $conn->real_escape_string($_POST["next_hearing"]);
    $status = 'open';

    $sql = "INSERT INTO cases (case_number, client_name, lawyer_id, type, filing_date, next_hearing, status, created_at) 
            VALUES ('$caseNo', '$client', $lawyerId, '$caseType', '$filingDate', '$nextHearing', '$status', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'This script only accepts POST requests from the form.']);
    exit;
}
$conn->close();
?>