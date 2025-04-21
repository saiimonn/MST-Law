<?php 
session_start();
if (!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/adminHome.css">
    <link rel = "stylesheet" href = "../css/output.css">
</head>
<body>
    <?php include_once("../includedFiles/sidebar.php")?>

    <main class="dashboard">
        <header>
            <h3 class = "text-xl font-bold">Welcome, <?php echo $_SESSION["user_name"];?></h3>

            <nav class = "right">
                <span id = "hours">00</span>
                <span>:</span>
                <span id = "minutes">00</span>
                <span>:</span>
                <span id = "seconds">00</span>
                <span id = "session">AM</span>
            </nav>
        </header>

        <section class="dashboard-content">
            
            <div class="quick-actions">
                <h2 class = "text-lg font-semibold">Quick Actions</h2>
                <p class = "text-md text-gray-500">Manage users and view reports</p>
                <div class="quick-actions-box">
                <button class="btn-primary" onclick = "location.href='addUserForm.php'">Add User</button>
                <button class="btn-secondary" onclick = "location.href='userStatus.php'">Users</button>
                </div>
            </div>

            <div class="calendar">
                <div class="calendarInner">
                    <div class="header">
                        <button id = "prevBtn">
                            <i class = "fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="monthYear" id = "monthYear"></div>
                        <button id = "nextBtn">
                            <i class = "fa-solid fa-chevron-right"></i>
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
                    <div class="dates" id = "dates"></div>
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

            $query = "SELECT a.id, c.name AS client_name,
                        att.name AS attorney_name, 
                        a.date, a.start_time, a.end_time
                        FROM appointments a
                        JOIN users c ON a.user_id = c.id
                        JOIN users att ON a.attorney_id = att.id
                        WHERE a.status = 'confirmed'
                        AND a.date >= CURDATE()
                        ORDER BY a.date ASC";

            $result = $conn->query($query);
            ?>

            <div class="appointment-content" id = "upcoming">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="appointment-card">
                            <p><?php echo htmlspecialchars($row['client_name']); ?> with Attorney <?php echo htmlspecialchars($row['attorney_name']);?></p>
                            <p><?php echo $row['date']; ?> |⏰ <?php echo date("h:i A", strtotime($row['start_time']));?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                            <div class="buttons">
                                <button class="btn-danger" onclick="location.href='../includes/cancel_appointment.php?appointment_id=<?php echo $row['id']; ?>'">Cancel</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No upcoming appointments.</p>
                <?php endif; ?>
            </div>

            <?php 
            $query = "SELECT a.id, c.name AS client_name,
                        att.name AS attorney_name,
                        a.date, a.start_time, a.end_time
                        FROM appointments a
                        JOIN users c ON a.user_id = c.id
                        JOIN users att ON a.attorney_id = att.id
                        WHERE a.status = 'pending'
                        AND a.date >= CURDATE()
                        ORDER BY a.date ASC";
            
            $result = $conn->query($query);
            ?>

            <div class="appointment-content" id ="pending" style = "display: none;">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class = "appointment-card">
                            <p><?php echo htmlspecialchars($row['client_name']); ?> with Attorney <?php echo htmlspecialchars($row['attorney_name']); ?></p>
                            <p><?php echo $row['date']; ?> |⏰ <?php echo date("h:i A", strtotime($row['start_time'])); ?> - <?php echo date("h:i A", strtotime($row['end_time'])); ?></p>
                            <div class="buttons">
                                <button class="btn-success" onclick="location.href='../includes/approve_appointment.php?appointment_id=<?php echo $row['id']; ?>'">Approve</button>
                                <button class="btn-danger" onclick="location.href='../includes/cancel_appointment.php?appointment_id=<?php echo $row['id'];?>'">Reject</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No pending requests.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <script src = "../js/calendar.js"></script>
    <script src = "../js/clock.js"></script>
    <script src = "../js/appointmentTabs.js"></script>
    <script src = "../js/menuToggle.js"></script>
</body>
</html>
