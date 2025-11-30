<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, if not then redirect to student login page
if(!isset($_SESSION["student_logged_in"]) || $_SESSION["student_logged_in"] !== true){
    header("location: /online-skill-test-portal/student/login.php");
    exit;
}
?>