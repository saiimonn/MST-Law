<?php 
session_start();
if (!isset($_SESSION["user_name"]) || !isset($_SESSION["user_id"])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../logins/dbcon.php");

$user_id = $_SESSION['user_id'];

$updateStatus = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
$updateStatus->bind_param("i", $user_id);
$updateStatus->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel = "stylesheet" href = "../css/messages.css">
    <link rel = "stylesheet" href = "../css/output.css">
</head>
<body>

    <?php include_once("../includedFiles/header.php")?>
    <main class="dashboard">
        <header>
            <h3 class = "text-xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></h3>
            <nav class="right">
                <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                <span id="session">AM</span>
            </nav>
        </header>

        <section class="dashboard-content">
            <div class="quick-actions">
                <h2 class = "text-lg font-semibold">Quick Actions</h2>
                <p class = "text-md text-gray-600">Manage your cases and appointments</p>
                <div class="quick-actions-box">
                    <button class="btn-primary" onclick="location.href='cases.php'">View Cases</button>
                    <button class="btn-secondary" onclick="location.href='scheduleAppointment.php'">Schedule Appointment</button>
                    <button class ="btn-secondary" onclick = "location.href='todo.php'">To-do</button>
                </div>
            </div>

            <div class="calendar">
                <div class="calendarInner">
                    <div class="header">
                        <button id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="monthYear" id="monthYear"></div>
                        <button id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>
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

            <?php 
            include '../logins/dbcon.php';
            $user_id = $_SESSION['user_id'];

            // Debugging user ID
            echo "<!-- Debug: Lawyer ID = {$user_id} -->";

            // Fetch confirmed appointments
            $confirmed_query = "SELECT a.*, u.name AS client_name, u.gender
                      FROM appointments a 
                      JOIN users u ON a.user_id = u.id 
                      WHERE a.attorney_id = ?  /* Changed from user_id to attorney_id */
                      AND a.status = 'confirmed'  
                      AND a.date >= CURDATE()
                      ORDER BY a.date ASC";

            $stmt = $conn->prepare($confirmed_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $confirmed_result = $stmt->get_result();

            echo "<!-- Debug: Found " . $confirmed_result->num_rows . " confirmed appointments -->";
            ?>

            <div class="appointment-content" id="upcoming">
                <?php if ($confirmed_result && $confirmed_result->num_rows > 0): ?>
                    <?php while ($row = $confirmed_result->fetch_assoc()): ?>
                        <?php 
                            $title = ($row['gender'] === 'male') ? 'Mr.' : (($row['gender'] === 'female') ? 'Ms.' : '');    
                        ?>
                        <div class="appointment-card">
                            <p>👤 Appointment with <?php echo $title . ' ' . htmlspecialchars($row['client_name']); ?></p>
                            <p>📅 <?php echo $row['date']; ?> | ⏰ <?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                            <div class="buttons">
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

            <?php
            // Fetch pending requests
            $pending_query = "SELECT a.*, u.name AS client_name, u.gender
                      FROM appointments a
                      JOIN users u ON a.user_id = u.id
                      WHERE a.attorney_id = ?  
                      AND a.status = 'pending' 
                      AND a.date >= CURDATE()
                      ORDER BY a.date ASC";

            $stmt = $conn->prepare($pending_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $pending_result = $stmt->get_result();

            echo "<!-- Debug: Found " . $pending_result->num_rows . " pending requests -->";
            ?>

            <div class="appointment-content" id="pending" style="display: none;">
                <?php if ($pending_result && $pending_result->num_rows > 0): ?>
                    <?php while ($row = $pending_result->fetch_assoc()): ?>
                        <?php 
                        $title = ($row['gender'] === 'male') ? 'Mr.' : (($row['gender'] === 'female') ? 'Ms.' : '');    
                        ?>
                        <div class="appointment-card">
                            <p>👤 Pending request from <?php echo $title . ' ' .htmlspecialchars($row['client_name']); ?></p>
                            <p>📅 <?php echo $row['date']; ?> | ⏰ <?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                            <div class="buttons">
                                <button class="btn-secondary" style = "background-color: lightgreen;" onclick="location.href='../includes/handle_appointment_request.php?appointment_id=<?php echo $row['id']; ?>&action=confirmed'">Accept</button>
                                <button class="btn-danger" onclick="location.href='../includes/handle_appointment_request.php?appointment_id=<?php echo $row['id']; ?>&action=rejected'">Reject</button>
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
    <script src = "../js/reschedModal.js"></script>
</body>
</html>