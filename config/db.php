<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Default XAMPP password is empty
define('DB_NAME', 'online_skill_test_portal');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . $conn->connect_error);
}

// Set character set to utf8mb4 for proper handling of all characters
$conn->set_charset("utf8mb4");

// Helper function to safely close the connection if it's open
function close_db_connection($conn) {
    if ($conn instanceof mysqli && $conn->ping()) {
        $conn->close();
    }
}
?>