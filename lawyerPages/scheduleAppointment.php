<?php 
session_start();
if (!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../includes/db_connection.php");

$sql = "SELECT id, name
        FROM users
        WHERE role = 'client'
        ORDER BY name";

$stmt = $conn->prepare($sql);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel = "stylesheet" href = "../css/appointmentForm.css">
</head>
<body>
    <div class="container">
        <?php 
        if(isset($_SESSION['error'])) {
            echo "<div class='error'>{$_SESSION['error']}</div>";
            unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])) {
            echo "<div class='success-message'>
                    <div class='success-content'>
                        <i class='success-icon'>✓</i>
                        <p>{$_SESSION['success']}</p>
                        <a href='lawyerHome.php' class='back-button'>Back to Home</a>
                    </div>
                  </div>";
            unset($_SESSION['success']);
        }
        ?>

        <form action = "../includes/lawyerAppointmentFormVerify.php" method = "POST" class = "appointment-form">
            <h2>Schedule New Appointment</h2>
            <div class="form-group">
                <label for = "client">Select Client:</label>
                <select name = "client" id = "client" required>
                    <option value = "">Choose a client...</option>
                    <?php foreach($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>">
                            <?php echo htmlspecialchars($client['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for = "date">Appointment Date</label>
                <input type = "date"  id = "date" name = "date" required min = "<?php echo date('Y-m-d'); ?>"/>
            </div>

            <div class="form-group">
                <label for = "start_time">Start Time:</label>
                <input type = "time" id = "start_time" name = "start_time" required/>
            </div>

            <div class="form-group">
                <label for = "end_time">End Time:</label>
                <input type = "time" id = "end_time" name = "end_time" required/>
            </div>

            <div class="form-group">
                <label for = "description">Appointment Details (optional)</label>
                <textarea name="description" rows="4"></textarea>
            </div>

            <input type = "hidden" name = "attorney_id" value = "<?php echo $_SESSION['user_id']; ?>"/>
            <input type = "hidden" name = "status" value = "confirmed"/>

            <button type = "submit" class = "btn-submit">Schedule Appointment</button>
            <button class = "back-button" onclick="history.back()">Cancel</button>

        </form>
    </div>
    <script>
        document.querySelector('.appointment-form').addEventListener('submit', function(e) {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if(startTime >= endTime) {
                e.preventDefault();
                alert('End time  must be after start time!');
            }
        });
    </script>
</body>
</html>