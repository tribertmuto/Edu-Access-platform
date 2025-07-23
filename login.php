<?php
require 'db.php';

$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (!$email || !$password) {
    echo '<p style="color:red;">Please enter both email and password.</p>';
    exit;
}

$stmt = $conn->prepare("SELECT password, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($hash, $name);
    $stmt->fetch();
    if (password_verify($password, $hash)) {
        echo "<p style='color:green;'>Login successful! Welcome, " . htmlspecialchars($name) . "</p>";
    } else {
        echo "<p style='color:red;'>Incorrect password.</p>";
    }
} else {
    echo "<p style='color:red;'>No user found with that email.</p>";
}
$stmt->close();
$conn->close();
?> 