<link rel = "stylesheet" href = "../css/adminHome.css">

<div class="menu-toggle">
    <i class="fas fa-bars"></i>
</div>

<aside class="sidebar">
    <nav>
        <ul>
            <li><a href="../adminPages/adminHome.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'adminHome.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../adminPages/analytics.php" class = "nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">Analytics</a></li>
            <li><a href="../adminPages/schedules.php" class = "nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'schedules.php' ? 'active' : ''; ?>">Manage Schedule</a></li>
        </ul>
    </nav>
    <button class="logout" onclick="location.href='../logins/logout.php'">Logout</button>
</aside>