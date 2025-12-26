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
    
    // Verify that the habit belongs to the user
    $stmt = $conn->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Delete habit logs first (due to foreign key constraint)
        $delete_logs = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = ?");
        $delete_logs->bind_param("i", $habit_id);
        $delete_logs->execute();
        $delete_logs->close();
        
        // Delete the habit
        $delete_habit = $conn->prepare("DELETE FROM habits WHERE id = ?");
        $delete_habit->bind_param("i", $habit_id);
        $delete_habit->execute();
        $delete_habit->close();
        
        $_SESSION['success'] = "Habit deleted successfully!";
    } else {
        $_SESSION['error'] = "Invalid habit!";
    }
    $stmt->close();
}

header("Location: dashboard.php");
exit();

