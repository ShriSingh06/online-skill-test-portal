<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
$pageTitle = "Home | Online Skill Test";
include 'includes/header.php';
?>
<section class="hero-grid">
    <div class="hero-left glass-card">
        <h1>Online Student Skill Testing & Result Portal</h1>
        <p>
            A minimal and modern platfrorm for Quatitative Tests.
        </p>
        <div class="hero-actions">
            <a href="student_register.php" class="btn primary">Student Register</a>
            <a href="student_login.php" class="btn ghost">Student Login</a>
            <a href="admin_login.php" class="btn subtle">Admin Login</a>
        </div>
        <ul class="hero-bullets">
            <li>Timer-based MCQ tests</li>
            <li>Auto-submit when time ends</li>
            <li>Instant result generation</li>
            <li>Admin question & student management</li>
        </ul>
    </div>

    <div class="hero-right glass-card">
        <h2>System Overview</h2>
        <div class="pill-row">
            <span class="pill">Admin Dashboard</span>
            <span class="pill">Student Portal</span>
            <span class="pill">Analytics</span>
        </div>
        <p class="hero-note">
            “One place to manage questions, students, tests and results —
            built with PHP, MySQL, HTML, CSS & JavaScript.”
        </p>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
