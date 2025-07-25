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
('Digital Marketing Mastery', 'Learn SEO, content marketing, social media, and analytics to grow any brand online.', 149.00, 'Beginner'),
('UI/UX Design Fundamentals', 'Understand the principles of user interface and user experience design, and create stunning, user-friendly apps and websites.', 179.00, 'Beginner'),
('Cloud Computing Essentials', 'Get started with cloud platforms, virtualization, and deploying scalable applications on AWS, Azure, or Google Cloud.', 229.00, 'Intermediate'),
('Cybersecurity Basics', 'Learn the fundamentals of cybersecurity, ethical hacking, and how to protect systems from threats.', 199.00, 'Beginner');

-- (Optional) Sample user for testing
-- Password should be hashed in production; here it is hashed for demo
INSERT INTO users (name, email, password, age) VALUES
('Test User', 'test@example.com', '$2y$10$abcdefghijklmnopqrstuv', 25);

-- (Optional) Enroll sample user in a course
INSERT INTO user_enrollments (user_id, course_id) VALUES
(1, 1);
