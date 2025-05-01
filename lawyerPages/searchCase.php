<?php 
session_start();
require_once("../logins/dbcon.php");

header("Content-Type: application/json");

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$lawyer_id = $_SESSION['user_id'] ?? 0;

function renderCases($conn, $lawyer_id, $q, $status) {
    $sql = "SELECT * FROM cases WHERE lawyer_id = ? AND status = ? AND client_name LIKE ? ORDER BY filing_date DESC";
    $stmt = $conn->prepare($sql);
    $like = '%' . $q . '%';
    $stmt->bind_param("iss", $lawyer_id, $status, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows .= '<tr class="border-b border-gray-200 hover:bg-gray-50">';
            $rows .= '<td class="p-2 md:p-4">' . htmlspecialchars($row['case_number']) . '</td>';
            $rows .= '<td class="p-2 md:p-4">' . htmlspecialchars($row['client_name']) . '</td>';
            $rows .= '<td class="hidden sm:table-cell p-2 md:p-4">' . htmlspecialchars($row['type']) . '</td>';
            $rows .= '<td class="hidden md:table-cell p-2 md:p-4">' . htmlspecialchars($row['filing_date']) . '</td>';
            if ($status === 'open') {
                $rows .= '<td class="hidden md:table-cell p-2 md:p-4">' . htmlspecialchars($row['next_hearing']) . '</td>';
            }
            $rows .= '<td class="p-2 md:p-4">';
            $rows .= '<div class="relative inline-block text-left">';
            $rows .= '<button type="button" onclick="toggleDropdown(' . $row['id'] . ')" class="text-gray-500 hover:text-gray-700">';
            $rows .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">';
            $rows .= '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />';
            $rows .= '</svg></button>';
            $rows .= '<div id="dropdown-' . $row['id'] . '" class="hidden fixed mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black" style="z-index:9999;">';
            $rows .= '<div class="py-1" role="menu" aria-orientation="vertical">';
            if ($status === 'open') {
                $rows .= '<a href="../includes/closeCase.php?id=' . $row['id'] . '" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm(\'Are you sure you want to mark this case as closed?\')">Mark as Closed</a>';
            } else {
                $rows .= '<a href="../includes/reopenCase.php?id=' . $row['id'] . '" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm(\'Are you sure you want to reopen this case?\')">Reopen Case</a>';
            }
            $rows .= '<a href="../includes/deleteCase.php?id=' . $row['id'] . '" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" role="menuitem" onclick="return confirm(\'Are you sure you want to delete this case?\')">Delete Case</a>';
            $rows .= '<a href="#" onclick="showCaseDetails(' . $row['id'] . ')" class="block px-4 py-2 text-sm text-black hover:bg-gray-100 md:hidden" role="menuitem">View Details</a>';
            $rows .= '</div></div></div></td></tr>';
        }
    } else {
        $rows .= '<tr><td colspan="7" class="p-4 text-center text-gray-500">' . ($status === 'open' ? 'No open cases found' : 'No closed cases') . '</td></tr>';
    }
    $stmt->close();
    return $rows;
}

$openRows = renderCases($conn, $lawyer_id, $q, 'open');
$closedRows = renderCases($conn, $lawyer_id, $q, 'closed');

echo json_encode([
    'open' => $openRows,
    'closed' => $closedRows
]);
exit;