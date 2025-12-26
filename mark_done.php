<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $habit_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Get the date (default to today if not provided)
    $selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
    // Validate date format and ensure it's not in the future
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
        $_SESSION['error'] = "Invalid date format!";
        header("Location: dashboard.php");
        exit();
    }
    
    // Ensure date is not in the future
    if (strtotime($selected_date) > strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = "Cannot mark habits for future dates!";
        header("Location: dashboard.php");
        exit();
    }
    
    // Verify that the habit belongs to the user
    $stmt = $conn->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Check if already logged for this date
        $check_stmt = $conn->prepare("SELECT id FROM habit_logs WHERE habit_id = ? AND log_date = ?");
        $check_stmt->bind_param("is", $habit_id, $selected_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $log_stmt = $conn->prepare("INSERT INTO habit_logs (habit_id, log_date, status) VALUES (?, ?, 'completed')");
            $log_stmt->bind_param("is", $habit_id, $selected_date);
            $log_stmt->execute();
            $log_stmt->close();
            
            $date_display = date('M d, Y', strtotime($selected_date));
            if ($selected_date == date('Y-m-d')) {
                $_SESSION['success'] = "Habit marked as done!";
            } else {
                $_SESSION['success'] = "Habit marked as done for " . $date_display . "!";
            }
        } else {
            $date_display = date('M d, Y', strtotime($selected_date));
            if ($selected_date == date('Y-m-d')) {
                $_SESSION['error'] = "You've already marked this habit as done today!";
            } else {
                $_SESSION['error'] = "You've already marked this habit as done for " . $date_display . "!";
            }
        }
        $check_stmt->close();
    } else {
        $_SESSION['error'] = "Invalid habit!";
    }
    $stmt->close();
}

header("Location: dashboard.php");
exit();
