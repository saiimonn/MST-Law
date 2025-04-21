<?php 
session_start();
require_once("../logins/dbcon.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../logins/login.php");
    exit();
}

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../lawyerPages/cases.php");
    exit();
}

$case_id = $_GET['id'];
$lawyer_id = $_SESSION['user_id'];

$checkSQL = "SELECT id FROM cases
            WHERE id = ? AND lawyer_id = ?";
$checkStmt = $conn->prepare($checkSQL);
$checkStmt->bind_param("ii", $case_id, $lawyer_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if($checkResult->num_rows == 0) {
    $_SESSION['error_message'] = "You don't have permission to delete this case.";
    header("Location: ../lawyerPages/cases.php");
    exit();
}

$deleteSQL = "DELETE FROM cases
              WHERE id =?";
$deleteStmt = $conn->prepare($deleteSQL);
$deleteStmt->bind_param("i", $case_id);

if($deleteStmt->execute()) {
    $_SESSION['success_message'] = "Case deleted successfully.";
} else {
    $_SESSION['error_message'] = "Error deleting case: " . $conn->error;
}

header("Location: ../lawyerPages/cases.php");
exit();
?>