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

// Initialize variables
$name = '';
$email = '';
$password = '';
$age = null;
$course = '';
$errors = [];

// Validate and sanitize input data
if (isset($_POST['name'])) {
    $name = sanitizeInput($_POST['name']);
    if (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = "Name must be between 2 and 100 characters.";
    }
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
        $errors[] = "Name can only contain letters, spaces, hyphens, and apostrophes.";
    }
} else {
    $errors[] = "Name is required.";
}

if (isset($_POST['email'])) {
    $email = sanitizeInput($_POST['email']);
    if (!validateEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    } else if (strlen($email) > 100) {
        $errors[] = "Email address is too long.";
    }
} else {
    $errors[] = "Email is required.";
}

if (isset($_POST['password'])) {
    $password = $_POST['password']; // Don't sanitize password as it may contain special characters
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if (strlen($password) > 255) {
        $errors[] = "Password is too long.";
    }
    // Optional: Add more password strength requirements
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one letter and one number.";
    }
} else {
    $errors[] = "Password is required.";
}

// Optional fields
if (isset($_POST['age']) && !empty($_POST['age'])) {
    $age = (int) $_POST['age'];
    if ($age < 13 || $age > 120) {
        $errors[] = "Please enter a valid age between 13 and 120.";
    }
}

if (isset($_POST['course']) && !empty($_POST['course'])) {
    $course = sanitizeInput($_POST['course']);
    if (strlen($course) > 100) {
        $errors[] = "Course name is too long.";
    }
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
    $sql = "INSERT INTO users (name, email, password, age, course, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("sssis", $name, $email, $password_hash, $age, $course);
    
    // Execute the statement
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        // If a course is specified, add enrollment record
        if (!empty($course)) {
            $enroll_sql = "INSERT INTO user_enrollments (user_id, course, enrollment_date) VALUES (?, ?, NOW())";
            $enroll_stmt = $conn->prepare($enroll_sql);
            if ($enroll_stmt) {
                $enroll_stmt->bind_param("is", $user_id, $course);
                $enroll_stmt->execute();
                $enroll_stmt->close();
            }
        }
        
        // Success response
        echo '<div style="color:green; background:#e6ffe6; padding:15px; border-radius:5px; margin:10px 0;">';
        echo '<h4>✅ Registration Successful!</h4>';
        echo '<p>Welcome to EduAccess, <strong>' . htmlspecialchars($name) . '</strong>!</p>';
        echo '<p>Your account has been created successfully. You will be redirected shortly...</p>';
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
?>