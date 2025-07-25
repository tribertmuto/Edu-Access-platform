<?php
/**
 * Enhanced User Registration Script for EduAccess Platform
 * Handles user registration with proper validation and security
 */

// Include database connection
require_once 'db.php';

// Set response headers
header('Content-Type: text/html; charset=UTF-8');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<p style="color:red;">❌ Invalid request method.</p>';
    exit;
}

// Helper functions
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
function emailExists($email) {
    global $conn;
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}
function getCourseId($courseName) {
    global $conn;
    $stmt = $conn->prepare('SELECT id FROM courses WHERE course_name = ?');
    $stmt->bind_param('s', $courseName);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    return $id ?? null;
}

// Initialize variables
$name = '';
$email = '';
$password = '';
$age = null;
$course = '';
$errors = [];

// Input validation
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$age = isset($_POST['age']) && $_POST['age'] !== '' ? (int)$_POST['age'] : null;
$course = isset($_POST['course']) ? sanitizeInput($_POST['course']) : '';

if (strlen($name) < 2 || strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters.';
}
if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
    $errors[] = 'Name can only contain letters, spaces, hyphens, and apostrophes.';
}
if (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
} elseif (strlen($email) > 100) {
    $errors[] = 'Email address is too long.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters long.';
}
if (strlen($password) > 255) {
    $errors[] = 'Password is too long.';
}
if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must contain at least one letter and one number.';
}
if ($age !== null && ($age < 13 || $age > 120)) {
    $errors[] = 'Please enter a valid age between 13 and 120.';
}
if ($course && strlen($course) > 100) {
    $errors[] = 'Course name is too long.';
}

// Display validation errors
if (!empty($errors)) {
    echo '<div style="color:red; background:#ffe6e6; padding:15px; border-radius:5px; margin:10px 0;">';
    echo '<h4>❌ Registration Failed</h4>';
    echo '<ul style="margin:10px 0; padding-left:20px;">';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    exit;
}

try {
    // Check if email already exists
    if (emailExists($email)) {
        echo '<div style="color:red; background:#ffe6e6; padding:15px; border-radius:5px; margin:10px 0;">';
        echo '<h4>❌ Registration Failed</h4>';
        echo '<p>An account with this email address already exists. Please <a href="auth.html">login</a> instead.</p>';
        echo '</div>';
        exit;
    }
    
    // Hash the password
    $password_hash = hashPassword($password);
    
    // Prepare SQL statement
    $sql = "INSERT INTO users (name, email, password, age, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("sssi", $name, $email, $password_hash, $age);
    
    // Execute the statement
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        // If a course is specified, add enrollment record
        if (!empty($course)) {
            $course_id = getCourseId($course);
            if ($course_id) {
                $enroll_sql = "INSERT INTO user_enrollments (user_id, course_id, enrolled_at) VALUES (?, ?, NOW())";
                $enroll_stmt = $conn->prepare($enroll_sql);
                if ($enroll_stmt) {
                    $enroll_stmt->bind_param("ii", $user_id, $course_id);
                    $enroll_stmt->execute();
                    $enroll_stmt->close();
                }
            }
        }
        
        // Success response
        echo '<div style="color:green; background:#e6ffe6; padding:15px; border-radius:5px; margin:10px 0;">';
        echo '<h4>✅ Registration successful!</h4>';
        echo '<p>Welcome to EduAccess, <strong>' . htmlspecialchars($name) . '</strong>!</p>';
        if (!empty($course)) {
            echo '<p>You have been enrolled in: <strong>' . htmlspecialchars($course) . '</strong></p>';
        }
        echo '</div>';
        
        // Log successful registration (in production, use proper logging)
        error_log("New user registered: " . $email . " (ID: " . $user_id . ")");
        
    } else {
        throw new Exception("Failed to execute registration: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    // Log error
    error_log("Registration error: " . $e->getMessage());
    
    echo '<div style="color:red; background:#ffe6e6; padding:15px; border-radius:5px; margin:10px 0;">';
    echo '<h4>❌ Registration Failed</h4>';
    
    if (in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1', '::1'])) {
        // Development environment - show detailed error
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    } else {
        // Production environment - show generic error
        echo '<p>We encountered an error while creating your account. Please try again later.</p>';
    }
    
    echo '</div>';
}
$conn->close();
?>