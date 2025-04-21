<?php 
session_start();
if(!isset($_SESSION['user_name'])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../includes/db_connection.php");

$attorney_id = $_SESSION['user_id'];

$sql = "SELECT DISTINCT u.id, u.name, u.email, u.phone
        FROM users u
        INNER JOIN appointments a ON u.id = a.user_id
        WHERE a.attorney_id = ?
        AND a.status = 'confirmed'
        AND u.role = 'client'
        ORDER BY u.name";

$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $attorney_id);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link rel = "stylesheet" href = "../css/home.css">
    <link rel = "stylesheet" href = "../css/client-attorneys.css">
</head>
<body>

    <?php include_once("../includedFiles/header.php")?>

    <main class = "dashboard">
        <header>
            <h3>Your clients</h3>
        </header>

        <section class="lawyerContent">
            <?php foreach($clients as $client) :?>
                <div class="infoBox">
                    <div class="boxContent">
                        <div class="lawyer-info">
                            <h4><?php echo htmlspecialchars($client['name'] ?? 'N/A'); ?></h4>
                            <p><span class = "label">Email: </span><?php echo htmlspecialchars($client['email'] ?? 'N/A'); ?></p>
                            <p><span class = "label">Phone: </span><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <script src = "../js/menuToggle.js"></script>
</body>
</html>