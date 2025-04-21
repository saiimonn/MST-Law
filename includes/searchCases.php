<?php 
session_start();
require_once("../logins/dbcon.php");

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$lawyer_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'open';

if($status !== 'open' && $status !== 'closed') {
    $statut = 'open';
}

$sql = "SELECT * FROM cases
        WHERE lawyer_id = ? 
        AND status = ? 
        AND (case_number LIKE ? OR client_name
        LIKE ? )
        ORDER BY filing_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $lawyer_id, $status, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$cases = [];
while($row = $result->fetch_assoc()) {
    $cases[] = $row;
}

echo json_encode($cases);
$stmt->close();
$conn->close();
?>