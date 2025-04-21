<?php
session_start();
require_once("../logins/dbcon.php");

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT * FROM users WHERE (role = 'lawyer' OR role = 'client')";
if ($search !== '') {
    $sql .= " AND name LIKE ?";
}

$stmt = $conn->prepare($sql);
if ($search !== '') {
    $like = "%$search%";
    $stmt->bind_param("s", $like);
}
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()): ?>
    <div class="grid grid-cols-10 gap-2 p-4 font-medium border-t user-row" data-role="<?php echo htmlspecialchars($row['role']); ?>">
        <div class="col-span-3"><?php echo htmlspecialchars($row['name']); ?></div>
        <div class="col-span-2"><?php echo htmlspecialchars($row['role']); ?></div>
        <div class="col-span-3"><?php echo htmlspecialchars($row['email']); ?></div>
        <div class="col-span-2">
            <?php if($row['status'] === 'active'):?>
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-green-500 hover:bg-green-600">Active</div>
            <?php else: ?>
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 border-transparent text-white bg-red-500 hover:bg-red-600">Inactive</div>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile;
$stmt->close();
$conn->close();
?>