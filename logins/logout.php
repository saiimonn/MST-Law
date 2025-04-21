<?php 
session_start();
require_once("../logins/dbcon.php");

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $updateStatus = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $updateStatus->bind_param("i", $user_id);
    $updateStatus->execute();
}

$_SESSION = array();
session_destroy();

header('Location: login.php');
exit();
?>