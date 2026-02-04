<?php
// config.php
session_start();

// change this according to your setup:
$base_url = "http://localhost/online-skill-test/"; 
// On hosting: e.g. "https://yourdomain.com/"

// Detect if running on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // LOCAL XAMPP URL
    $base_url = 'http://localhost/online-skill-test';
} else {
    // LIVE InfinityFree URL  (change this to your real URL)
    $base_url = 'https://onlineskilltest.42web.io';
}

$host = "sql100.infinityfree.com";
$user = "if0_40605185";       // XAMPP default
$pass = "7CTXPGx3gPoei";           // XAMPP default
$db   = "if0_40605185_skilltest_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
?>

