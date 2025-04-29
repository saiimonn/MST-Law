<?php 
session_start();
if(!isset($_SESSION['user_name'])) {
    header('Location: ../logins/login.php');
    exit();
}

require_once("../includes/db_connection.php");

// Fetch lawyers
$sql = "SELECT id, name FROM users WHERE role = 'Lawyer'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="../css/appointmentForm.css">
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
                        <a href='clientHome.php' class='back-button'>Back to Home</a>
                    </div>
                  </div>";
            unset($_SESSION['success']);
        }
        ?>
        
        <form method="POST" action="../includes/appointmentFormVerify.php" enctype="multipart/form-data">
            <h2>Schedule an Appointment</h2>
            
            <div class="form-group">
                <label>Select Attorney</label>
                <select name="attorney" required>
                    <option value="">Choose an attorney...</option>
                    <?php foreach($lawyers as $lawyer): ?>
                        <option value="<?php echo htmlspecialchars($lawyer['id']); ?>">
                            <?php echo htmlspecialchars($lawyer['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" required>
            </div>

            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" required>
            </div>

            <div class="form-group">
                <label>Case Description (optional)</label>
                <textarea name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Upload Document (Optional - Max 5MB)</label>
                <div class="file-upload-container">
                    <input type="file" name="document" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" 
                           class="input file-input">
                    <p class="file-info">Supported formats: PDF, DOC, DOCX, JPG, PNG, ZIP (Max 5MB)</p>
                </div>
            </div>

            <button type="submit">Schedule Appointment</button>
            <button class="back-button" onclick="location.href='clientHome.php'">Back to Home</button>
        </form>
    </div>

    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const start = document.querySelector('input[name="start_time"]').value;
        const end = document.querySelector('input[name="end_time"]').value;
        
        if (start >= end) {
            e.preventDefault();
            alert('End time must be after start time');
        }
    });
    </script>
</body>
</html>