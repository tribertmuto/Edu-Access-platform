<?php
// Enable strict error reporting for development
// Comment out the next line in production
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$username = "root";
$password = ""; // Set this if your MySQL has a password
$database = "edu_platform_db";

try {
    // Connect to MySQL
    $conn = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    // In production, log the error and show a generic message
    error_log("Database connection error: " . $e->getMessage());
    
    // For development, show the actual error
    if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
        die("<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>");
    } else {
        // In production, show generic error
        die("<p style='color:red;'>❌ Database connection failed. Please try again later.</p>");
    }
} // end of connection
?>