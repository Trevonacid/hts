-- Full database setup for Habit Tracker (users, habits, logs, admin column)

CREATE DATABASE IF NOT EXISTS habit_tracker;
USE habit_tracker;

-- Users table (includes is_admin and created_at for future use)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Habits table
DROP TABLE IF EXISTS habits;
CREATE TABLE habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    habit_name VARCHAR(100),
    habit_type ENUM('good','bad') NOT NULL DEFAULT 'good',
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Habit logs table
DROP TABLE IF EXISTS habit_logs;
CREATE TABLE habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT,
    log_date DATE,
    status VARCHAR(20),
    FOREIGN KEY (habit_id) REFERENCES habits(id)
);

-- NOTE:
-- 1. To create the first admin user, run create_admin.php in your browser
--    after importing this file.
-- 2. Or manually insert a user with is_admin = 1.
-- 3. Application code handles password hashing with password_hash()
--    before inserting into this table, do not store plain passwords.


