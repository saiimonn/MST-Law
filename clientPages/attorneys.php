<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../includes/db_connection.php");

// Fetch all lawyers from the database using PDO
$sql = "SELECT * FROM users WHERE role = 'Lawyer'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Attorneys</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/client-attorneys.css">
</head>
<body>

    <?php include_once("../includedFiles/header.php")?>

    <main class="dashboard">
        <header>
            <h3>Our Attorneys</h3>
        </header>    

        <section class="lawyerContent" id="dashboard-content">
            <?php foreach($lawyers as $lawyer): ?>
                <div class="infoBox">
                    <div class="boxContent">
                        <div class="lawyer-info">
                            <h4><?php echo htmlspecialchars($lawyer['name'] ?? 'N/A'); ?></h4>
                            <p><?php echo htmlspecialchars($lawyer['email'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <script src = "../js/menuToggle.js"></script>
</body>
</html>