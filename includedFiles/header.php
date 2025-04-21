<header class="main-header">
    <div class="menu-icon" id = "menuToggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="logo">
        <h1>MST Law Firm</h1>
    </div>
    <nav class="main-nav" id = "mainNav">
        <ul>
            <?php if(strpos($_SERVER['PHP_SELF'], 'clientPages') !== false): ?>
                <li><a href="clientHome.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientHome.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">Messages</a></li>
                <li><a href="documents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>">Documents</a></li>
                <li><a href="attorneys.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'attorneys.php' ? 'active' : ''; ?>">Attorneys</a></li>
            <?php else: ?>
                <li><a href="lawyerHome.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'lawyerHome.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">Messages</a></li>
                <li><a href="documents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>">Documents</a></li>
                <li><a href="clients.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'active' : ''; ?>">Clients</a></li>
            <?php endif; ?>
        </ul>

        <div class="user-actions">
            <button class="logout" onclick="location.href='../logins/logout.php'">Logout</button>
        </div>
    </nav>
    <script src = "../js/headerToggle.js"></script>
</header>