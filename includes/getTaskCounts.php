<?php
session_start();
require_once("../logins/dbcon.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// TODAY
$sqlToday = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) = ? AND status != 'completed'";
$stmtToday = $conn->prepare($sqlToday);
$stmtToday->bind_param("is", $user_id, $today);
$stmtToday->execute();
$stmtToday->bind_result($todayCount);
$stmtToday->fetch();
$stmtToday->close();

// UPCOMING
$sqlUpcoming = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) > ? AND status != 'completed'";
$stmtUpcoming = $conn->prepare($sqlUpcoming);
$stmtUpcoming->bind_param("is", $user_id, $today);
$stmtUpcoming->execute();
$stmtUpcoming->bind_result($upcomingCount);
$stmtUpcoming->fetch();
$stmtUpcoming->close();

// OVERDUE
$sqlOverdue = "SELECT COUNT(*) FROM tasks WHERE lawyer_id = ? AND DATE(deadline) < ? AND status != 'completed'";
$stmtOverdue = $conn->prepare($sqlOverdue);
$stmtOverdue->bind_param("is", $user_id, $today);
$stmtOverdue->execute();
$stmtOverdue->bind_result($overdueCount);
$stmtOverdue->fetch();
$stmtOverdue->close();

echo json_encode([
    'today' => $todayCount,
    'upcoming' => $upcomingCount,
    'overdue' => $overdueCount
]);

$conn->close();
?>