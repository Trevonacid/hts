<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add'])) {
    $habit = trim($_POST['habit'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Comprehensive validation
    $errors = [];
    
    if (empty($habit)) {
        $errors[] = "Habit name cannot be empty!";
    } elseif (strlen($habit) < 2) {
        $errors[] = "Habit name must be at least 2 characters long!";
    } elseif (strlen($habit) > 100) {
        $errors[] = "Habit name must not exceed 100 characters!";
    } elseif (!preg_match("/^[a-zA-Z0-9\s\-_.,!?()]+$/u", $habit)) {
        $errors[] = "Habit name contains invalid characters! Only letters, numbers, spaces, and basic punctuation are allowed.";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO habits (user_id, habit_name, created_at) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("is", $user_id, $habit);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Habit added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add habit. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = $errors[0];
    }
}

header("Location: dashboard.php");
exit();
