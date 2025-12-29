<?php
session_start();
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$habit_id = isset($_GET['habit_id']) ? intval($_GET['habit_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($habit_id <= 0) {
    $_SESSION['error'] = "Invalid habit ID!";
    header("Location: admin_dashboard.php");
    exit();
}

// Verify habit exists
$stmt = $conn->prepare("SELECT id, habit_name FROM habits WHERE id = ?");
$stmt->bind_param("i", $habit_id);
$stmt->execute();
$result = $stmt->get_result();
$habit = $result->fetch_assoc();
$stmt->close();

if (!$habit) {
    $_SESSION['error'] = "Habit not found!";
    if ($user_id > 0) {
        header("Location: admin_view_user.php?id=" . $user_id);
    } else {
        header("Location: admin_dashboard.php");
    }
    exit();
}

// Delete habit logs first
$delete_logs = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = ?");
$delete_logs->bind_param("i", $habit_id);
$delete_logs->execute();
$delete_logs->close();

// Delete the habit
$delete_habit = $conn->prepare("DELETE FROM habits WHERE id = ?");
$delete_habit->bind_param("i", $habit_id);
$delete_habit->execute();
$delete_habit->close();

$_SESSION['success'] = "Habit '" . htmlspecialchars($habit['habit_name']) . "' has been deleted successfully!";

if ($user_id > 0) {
    header("Location: admin_view_user.php?id=" . $user_id);
} else {
    header("Location: admin_dashboard.php");
}
exit();

