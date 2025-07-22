<?php
require 'db.php';

$name = trim($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$age = (int) $_POST['age'];
$course = htmlspecialchars($_POST['course']);

if (!$email || !$name || !$age || !$course) {
    die("Invalid input.");
}

$stmt = $conn->prepare("INSERT INTO users (name, email, age, course) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $name, $email, $age, $course);

if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
