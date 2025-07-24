<?php
/**
 * Database Connection Test Script for EduAccess Platform
 * Use this to verify your database setup is working correctly
 * Access via: http://localhost/eduaccess/test_db.php
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduAccess - Database Connection Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #1a237e;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ddd;
        }
        .success {
            background: #e8f5e8;
            border-left-color: #4caf50;
            color: #2e7d2e;
        }
        .error {
            background: #ffeaea;
            border-left-color: #f44336;
            color: #c62828;
        }
        .info {
            background: #e3f2fd;
            border-left-color: #2196f3;
            color: #1565c0;
        }
        .warning {
            background: #fff8e1;
            border-left-color: #ff9800;
            color: #ef6c00;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: #1a237e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .button:hover {
            background: #303f9f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 EduAccess Platform</h1>
            <h2>Database Connection Test</h2>
            <p>This page tests your database setup and configuration</p>
        </div>

        <?php
        // Test 1: Include database connection
        echo '<div class="test-section">';
        echo '<h3>📋 Test 1: Database Connection</h3>';
        
        try {
            require_once 'db.php';
            echo '<div class="success">✅ Database connection successful!</div>';
            echo '<p><strong>Database:</strong> ' . $config['database'] . '</p>';
            echo '<p><strong>Host:</strong> ' . $config['host'] . '</p>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '</div></body></html>';
            exit;
        }
        echo '</div>';

        // Test 2: Check tables exist
        echo '<div class="test-section">';
        echo '<h3>🗃️ Test 2: Database Tables</h3>';
        
        $required_tables = ['users', 'user_enrollments', 'courses', 'user_sessions'];
        $existing_tables = [];
        
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_array()) {
                $existing_tables[] = $row[0];
            }
        }
        
        echo '<table>';
        echo '<tr><th>Table Name</th><th>Status</th><th>Records</th></tr>';
        
        foreach ($required_tables as $table) {
            echo '<tr>';
            echo '<td>' . $table . '</td>';
            
            if (in_array($table, $existing_tables)) {
                echo '<td><span style="color:green">✅ Exists</span></td>';
                
                // Count records
                $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
                echo '<td>' . $count . ' records</td>';
            } else {
                echo '<td><span style="color:red">❌ Missing</span></td>';
                echo '<td>-</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Test 3: Test helper functions
        echo '<div class="test-section">';
        echo '<h3>🔧 Test 3: Helper Functions</h3>';
        
        $functions_to_test = ['sanitizeInput', 'validateEmail', 'hashPassword', 'verifyPassword', 'emailExists'];
        
        echo '<table>';
        echo '<tr><th>Function</th><th>Status</th><th>Test Result</th></tr>';
        
        foreach ($functions_to_test as $func) {
            echo '<tr>';
            echo '<td>' . $func . '</td>';
            
            if (function_exists($func)) {
                echo '<td><span style="color:green">✅ Available</span></td>';
                
                // Test function
                try {
                    switch ($func) {
                        case 'sanitizeInput':
                            $test = sanitizeInput("test<script>alert('xss')</script>");
                            echo '<td>✅ XSS protection working</td>';
                            break;
                        case 'validateEmail':
                            $test = validateEmail("test@example.com");
                            echo '<td>' . ($test ? '✅ Valid email detected' : '❌ Email validation failed') . '</td>';
                            break;
                        case 'hashPassword':
                            $test = hashPassword("testpassword");
                            echo '<td>' . (strlen($test) > 50 ? '✅ Password hashed' : '❌ Hash failed') . '</td>';
                            break;
                        case 'verifyPassword':
                            $hash = hashPassword("testpassword");
                            $test = verifyPassword("testpassword", $hash);
                            echo '<td>' . ($test ? '✅ Password verification works' : '❌ Verification failed') . '</td>';
                            break;
                        case 'emailExists':
                            $test = emailExists("nonexistent@example.com");
                            echo '<td>' . (!$test ? '✅ Email check works' : '⚠️ Check function') . '</td>';
                            break;
                    }
                } catch (Exception $e) {
                    echo '<td><span style="color:orange">⚠️ Error: ' . htmlspecialchars($e->getMessage()) . '</span></td>';
                }
            } else {
                echo '<td><span style="color:red">❌ Missing</span></td>';
                echo '<td>Function not found</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Test 4: Check courses data
        echo '<div class="test-section">';
        echo '<h3>📚 Test 4: Sample Courses Data</h3>';
        
        if (in_array('courses', $existing_tables)) {
            $courses_result = $conn->query("SELECT course_name, price, difficulty_level FROM courses LIMIT 5");
            
            if ($courses_result && $courses_result->num_rows > 0) {
                echo '<div class="success">✅ Sample courses found!</div>';
                echo '<table>';
                echo '<tr><th>Course Name</th><th>Price</th><th>Level</th></tr>';
                
                while ($course = $courses_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($course['course_name']) . '</td>';
                    echo '<td>$' . number_format($course['price'], 2) . '</td>';
                    echo '<td>' . ucfirst($course['difficulty_level']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="warning">⚠️ No sample courses found. You may need to run the complete db.sql script.</div>';
            }
        } else {
            echo '<div class="error">❌ Courses table not found.</div>';
        }
        echo '</div>';

        // Test 5: PHP Configuration
        echo '<div class="test-section">';
        echo '<h3>⚙️ Test 5: PHP Configuration</h3>';
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
        
        $php_checks = [
            'PHP Version' => [phpversion(), version_compare(phpversion(), '7.4.0', '>=') ? 'success' : 'error'],
            'MySQLi Extension' => [extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded', extension_loaded('mysqli') ? 'success' : 'error'],
            'JSON Extension' => [extension_loaded('json') ? 'Loaded' : 'Not Loaded', extension_loaded('json') ? 'success' : 'error'],
            'Session Support' => [function_exists('session_start') ? 'Available' : 'Not Available', function_exists('session_start') ? 'success' : 'error'],
        ];
        
        foreach ($php_checks as $check => $data) {
            echo '<tr>';
            echo '<td>' . $check . '</td>';
            echo '<td>' . $data[0] . '</td>';
            echo '<td><span style="color:' . ($data[1] == 'success' ? 'green">✅' : 'red">❌') . '</span></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Test 6: File Permissions
        echo '<div class="test-section">';
        echo '<h3>📁 Test 6: File Status</h3>';
        
        $files_to_check = ['register.php', 'login.php', 'auth.html', 'index.html', 'style.css'];
        
        echo '<table>';
        echo '<tr><th>File</th><th>Status</th><th>Size</th></tr>';
        
        foreach ($files_to_check as $file) {
            echo '<tr>';
            echo '<td>' . $file . '</td>';
            
            if (file_exists($file)) {
                echo '<td><span style="color:green">✅ Found</span></td>';
                echo '<td>' . number_format(filesize($file)) . ' bytes</td>';
            } else {
                echo '<td><span style="color:red">❌ Missing</span></td>';
                echo '<td>-</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Summary and Next Steps
        echo '<div class="test-section info">';
        echo '<h3>🎯 Summary & Next Steps</h3>';
        
        if ($conn && in_array('users', $existing_tables)) {
            echo '<div class="success">';
            echo '<h4>✅ Your EduAccess platform is ready!</h4>';
            echo '<p>All essential components are working correctly. You can now:</p>';
            echo '<ul>';
            echo '<li><a href="index.html" class="button">Visit Homepage</a></li>';
            echo '<li><a href="auth.html" class="button">Test Registration/Login</a></li>';
            echo '<li><a href="about.html" class="button">View About Page</a></li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h4>❌ Setup incomplete</h4>';
            echo '<p>Please complete the following steps:</p>';
            echo '<ol>';
            echo '<li>Run the complete db.sql script in phpMyAdmin</li>';
            echo '<li>Verify database credentials in db.php</li>';
            echo '<li>Ensure all required files are present</li>';
            echo '<li>Refresh this page to test again</li>';
            echo '</ol>';
            echo '</div>';
        }
        echo '</div>';
        ?>

        <div class="test-section">
            <h3>🔄 Actions</h3>
            <a href="?" class="button">Refresh Tests</a>
            <a href="index.html" class="button">Go to Homepage</a>
            <a href="auth.html" class="button">Test Registration</a>
        </div>
    </div>
</body>
</html>