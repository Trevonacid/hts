CREATE DATABASE habit_tracker;
USE habit_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    habit_name VARCHAR(100),
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT,
    log_date DATE,
    status VARCHAR(20),
    FOREIGN KEY (habit_id) REFERENCES habits(id)
);
