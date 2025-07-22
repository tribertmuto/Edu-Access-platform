CREATE DATABASE edu_platform;
GO
USE edu_platform;
GO

CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    email NVARCHAR(100) NOT NULL UNIQUE,
    age INT NOT NULL,
    course NVARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT GETDATE()
);
CREATE TABLE user_enrollments (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    course NVARCHAR(50) NOT NULL,
    enrolled_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

