<nav class="sidebar">
    <div class="logo">Student Portal</div>
    <ul class="nav-list">
        <li><a href="/online-skill-test-portal/student/dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="/online-skill-test-portal/student/profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">Profile</a></li>
        <li><a href="/online-skill-test-portal/student/start_test.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'start_test.php' || basename($_SERVER['PHP_SELF']) == 'test.php') ? 'active' : ''; ?>">Take Test</a></li>
        <li><a href="/online-skill-test-portal/student/results.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'results.php') ? 'active' : ''; ?>">My Results</a></li>
    </ul>
    <div class="nav-footer">
        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['student_username']); ?></strong></p>
        <a href="/online-skill-test-portal/student/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>