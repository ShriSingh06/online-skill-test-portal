<?php
/**
 * Online Student Skill Testing & Result Portal
 * * ENVIRONMENT AND RUN INSTRUCTIONS:
 * * 1. Project Setup:
 * - Place the entire 'online-skill-test-portal' folder into your web server's document root (e.g., XAMPP's 'htdocs' folder).
 * * 2. Database Setup:
 * - Start your XAMPP Apache and MySQL services.
 * - Open phpMyAdmin (usually at http://localhost/phpmyadmin/).
 * - Create a new database named 'online_skill_test_portal'.
 * - Import the 'database.sql' file into this new database.
 * * 3. Configuration:
 * - Review and update the database credentials in 'config/db.php' if your MySQL setup is different from the default (username 'root', password '').
 * * 4. Default Admin Credentials:
 * - Username: admin
 * - Password: Admin@123
 * * 5. Access the Application:
 * - Open your web browser and navigate to: http://localhost/online-skill-test-portal/
 * * * This file (index.php) serves as a simple landing/redirect page.
 */

// Simple redirect to the student login page as the default entry point.
header("Location: student/login.php");
exit;
?>