<nav class="sidebar">
    <div class="logo">Admin Portal</div>
    <ul class="nav-list">
        <li><a href="/online-skill-test-portal/admin/dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="/online-skill-test-portal/admin/questions_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'questions_list.php' || basename($_SERVER['PHP_SELF']) == 'questions_add.php' || basename($_SERVER['PHP_SELF']) == 'questions_edit.php') ? 'active' : ''; ?>">Questions</a></li>
        <li><a href="/online-skill-test-portal/admin/students_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'students_list.php') ? 'active' : ''; ?>">Students</a></li>
        <li><a href="/online-skill-test-portal/admin/results_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'results_list.php') ? 'active' : ''; ?>">Results</a></li>
        <li><a href="/online-skill-test-portal/admin/settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">Settings</a></li>
    </ul>
    <div class="nav-footer">
        <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></p>
        <a href="/online-skill-test-portal/admin/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>