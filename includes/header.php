<?php
if (!isset($pageTitle)) {
    $pageTitle = "Online Student Skill Testing & Result Portal";
}
require_once __DIR__ . "/../config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="bg-gradient"></div>

<header class="glass-header">
    <div class="container header-inner">
        <div class="logo">SkillTest<span>OS</span></div>
        <nav class="nav-links">
            <a href="<?php echo $base_url; ?>">Home</a>

            <?php if (isset($_SESSION['student_id'])): ?>
                <a href="<?php echo $base_url; ?>student/dashboard.php">Student</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['admin_id'])): ?>
                <a href="<?php echo $base_url; ?>admin/dashboard.php">Admin</a>
            <?php endif; ?>

            <?php if (!isset($_SESSION['student_id']) && !isset($_SESSION['admin_id'])): ?>
                <a href="<?php echo $base_url; ?>student_login.php">Student Login</a>
                <a href="<?php echo $base_url; ?>admin_login.php">Admin Login</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="page">
    <div class="container">
