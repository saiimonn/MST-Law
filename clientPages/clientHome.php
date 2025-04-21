<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel = "stylesheet" href = "../css/messages.css">
</head>
<body>
<?php 
    session_start();
    if (!isset($_SESSION["user_name"])) {
        header("Location: ../logins/login.php");
        exit();
    }
    
    include '../logins/dbcon.php';

    $user_id = $_SESSION['user_id'];
    $query = "SELECT gender 
            FROM users 
            WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $title = ($user['gender'] === 'male') ? 'Mr.' : 
            (($user['gender'] === 'female') ? 'Ms.' : '');

    $updateStatus = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $updateStatus->bind_param("i", $user_id);
    $updateStatus->execute();
?>

<?php include_once "../includedFiles/header.php"?>

<main class="dashboard">
    <header>
        <h3>Welcome, <?php echo $title . ' ' . $_SESSION["user_name"]; ?></h3>
        <nav class="right">
            <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
            <span id="session">AM</span>
        </nav>
    </header>

    <section class="dashboard-content" id="dashboard-content">
        <!-- Dynamic content will be loaded here -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <p>Schedule and manage your appointments</p>
            <div class="quick-actions-box">
                <button class="btn-primary" onclick="location.href='newAppointment.php'">Request New Appointment</button>
            </div>
        </div>

        <div class="calendar">
            <div class="calendarInner">
                <div class="header">
                    <button id="prevBtn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="monthYear" id="monthYear"></div>
                    <button id="nextBtn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <div class="days">
                    <div class="day">Sun</div>
                    <div class="day">Mon</div>
                    <div class="day">Tue</div>
                    <div class="day">Wed</div>
                    <div class="day">Thu</div>
                    <div class="day">Fri</div>
                    <div class="day">Sat</div>
                </div>
                <div class="dates" id="dates"></div>
            </div>
        </div>
    </section>

    <section class="appointments">
        <div class="appointment-tabs">
            <button class="active" data-tab="upcoming">Upcoming Appointments</button>
            <button data-tab="pending">Pending Requests</button>
        </div>

        <!-- Below is the upcoming appointments content -->

        <?php 

        $user_id = $_SESSION['user_id'];

        $query = "SELECT a.id, u.name AS attorney_name, a.date, a.start_time, a.end_time, u.gender
                  FROM appointments a
                  JOIN users u ON a.attorney_id = u.id
                  WHERE a.user_id = ? AND a.status = 'confirmed' AND a.date >= CURDATE()
                  ORDER BY a.date ASC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <div class="appointment-content" id="upcoming">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <p>👤 Appointment with Atty. <?php echo htmlspecialchars($row['attorney_name']); ?></p>
                        <p>📅 <?php echo $row['date']; ?> | ⏰ <?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                        <div class="buttons">
                            <button class="btn-secondary">Reschedule</button>
                            <button class="btn-danger" onclick="location.href='../includes/cancel_appointment.php?appointment_id=<?php echo $row['id']; ?>'">Cancel</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="appointment-card">
                    <p>No upcoming appointments.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Below is the pending requests content -->

        <?php
        $query = "SELECT a.id, u.name AS attorney_name, a.date, a.start_time, a.end_time
                  FROM appointments a
                  JOIN users u ON a.attorney_id = u.id
                  WHERE a.user_id = ? AND a.status = 'pending' AND a.date >= CURDATE()
                  ORDER BY a.date ASC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <div class="appointment-content" id="pending" style="display: none;">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <p>👤 Pending request with Atty. <?php echo htmlspecialchars($row['attorney_name']); ?></p>
                        <p>📅 <?php echo $row['date']; ?> | ⏰ <?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                        <div class="buttons">
                            <button class="btn-danger" onclick="location.href='../includes/cancel_appointment.php?appointment_id=<?php echo $row['id']; ?>'">Cancel Request</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="appointment-card">
                    <p>No pending appointment requests.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script src="../js/calendar.js"></script>
<script src="../js/clock.js"></script>
<script src="../js/appointmentTabs.js"></script>
<script src = "../js/menuToggle.js"></script>
</script>
</body>
</html>