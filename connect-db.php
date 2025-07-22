<?php
$host = "localhost";
$username = "root";
$password = ""; // Set this if your MySQL has a password
$database = "edu_platform";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

echo "<p style='color:green;'>✅ Connected to the database successfully!</p>";

// Optional: Query test
$sql = "SELECT COUNT(*) as total_users FROM users";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo "<p>Total users in the database: " . $row['total_users'] . "</p>";
} else {
    echo "<p>No user data found or table is empty.</p>";
}

$conn->close();
?>
