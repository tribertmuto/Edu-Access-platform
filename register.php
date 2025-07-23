<?php
require 'db.php';

// Validate and sanitize input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
$password = isset($_POST['password']) ? $_POST['password'] : '';
$age = isset($_POST['age']) ? (int) $_POST['age'] : 0;
$course = isset($_POST['course']) ? htmlspecialchars($_POST['course']) : '';

$errors = [];
if (!$name || strlen($name) < 2) {
    $errors[] = "Please enter a valid name.";
}
if (!$email) {
    $errors[] = "Please enter a valid email address.";
}
if (isset($_POST['password']) && strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}
if (isset($_POST['age']) && ($age < 10 || $age > 120)) {
    $errors[] = "Please enter a valid age.";
}
if (isset($_POST['course']) && !$course) {
    $errors[] = "Please select a course.";
}

if ($errors) {
    echo '<ul style="color:red;">';
    foreach ($errors as $err) {
        echo '<li>' . htmlspecialchars($err) . '</li>';
    }
    echo '</ul>';
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "<p style='color:red;'>A user with this email already exists.</p>";
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Hash password if provided
$password_hash = null;
if (isset($_POST['password'])) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
}

// Insert user
if ($password_hash !== null) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hash);
} else if (isset($_POST['age']) && isset($_POST['course'])) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, age, course) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $email, $age, $course);
} else {
    echo '<p style="color:red;">Invalid registration data.</p>';
    exit;
}

if ($stmt->execute()) {
    echo "<p style='color:green;'>Registration successful! Welcome, " . htmlspecialchars($name) . "</p>";
} else {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($conn->error) . "</p>";
}
$stmt->close();
$conn->close();
?>
