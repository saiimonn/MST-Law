<?php 
session_start();
if(!isset($_SESSION['user_name'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");

if(isset($_GET['id'])) {
    $case_id = $_GET['id'];
    $lawyer_id = $_SESSION['user_id'];

    $checkSQL = "SELECT * FROM cases
                WHERE id = ? AND lawyer_id = ?";
    $checkStmt = $conn->prepare($checkSQL);
    $checkStmt->bind_param("ii", $case_id, $lawyer_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if($result->num_rows > 0) {
        $updateSQL = "UPDATE cases
                    SET status = 'open'
                    WHERE id = ? AND lawyer_id = ?";
        $updateStmt = $conn->prepare($updateSQL);
        $updateStmt->bind_param("ii", $case_id, $lawyer_id);

        if($updateStmt->execute()) {
            header("Location: ../lawyerPages/cases.php?success=Case reopened successfully");
        } else {
            header("Location: ../lawyerPages/cases.php?error=Failed to reopen case.");
        }
    } else {
        header("Location: ../lawyerPages/cases.php?error=Unauthorized access");
    }
} else {
    header("Location: ../lawyerPages/cases.php?error=Invalid request");
}
?>