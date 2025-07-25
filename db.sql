-- Create the EduAccess platform database
CREATE DATABASE IF NOT EXISTS edu_platform_db;
USE edu_platform_db;

-- Users table: stores all registered users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    age INT,
    course VARCHAR(100), -- Optional: last course enrolled or interested in
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Courses table: stores all available courses
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(8,2) DEFAULT 0.00,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- User enrollments: tracks which users are enrolled in which courses
CREATE TABLE IF NOT EXISTS user_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Sample courses for testing
INSERT INTO courses (course_name, description, price, difficulty_level) VALUES
('Web Development Bootcamp', 'Learn HTML, CSS, JavaScript, React, and Node.js by building real-world projects from scratch.', 199.00, 'Beginner'),
('Data Science & Analytics', 'Master Python, machine learning, data visualization, and analytics to make data-driven decisions.', 249.00, 'Intermediate'),
('Digital Marketing Mastery', 'Learn SEO, content marketing, social media, and analytics to grow any brand online.', 149.00, 'Beginner');

-- (Optional) Sample user for testing
-- Password should be hashed in production; here it is plain for demo only
INSERT INTO users (name, email, password, age, course) VALUES
('Test User', 'test@example.com', 'password123', 25, 'Web Development Bootcamp');
