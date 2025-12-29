<?php
session_start();
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    $_SESSION['error'] = "Invalid user ID!";
    header("Location: admin_dashboard.php");
    exit();
}

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['admin_id']) {
    $_SESSION['error'] = "You cannot delete your own admin account!";
    header("Location: admin_dashboard.php");
    exit();
}

// Verify user exists and is not an admin
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? AND is_admin = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['error'] = "User not found or is an admin!";
    header("Location: admin_dashboard.php");
    exit();
}

// Get all habit IDs for this user
$habits_stmt = $conn->prepare("SELECT id FROM habits WHERE user_id = ?");
$habits_stmt->bind_param("i", $user_id);
$habits_stmt->execute();
$habits_result = $habits_stmt->get_result();
$habit_ids = [];
while ($row = $habits_result->fetch_assoc()) {
    $habit_ids[] = $row['id'];
}
$habits_stmt->close();

// Delete habit logs for all user's habits
if (!empty($habit_ids)) {
    $placeholders = implode(',', array_fill(0, count($habit_ids), '?'));
    $delete_logs = $conn->prepare("DELETE FROM habit_logs WHERE habit_id IN ($placeholders)");
    $delete_logs->bind_param(str_repeat('i', count($habit_ids)), ...$habit_ids);
    $delete_logs->execute();
    $delete_logs->close();
}

// Delete all habits for this user
$delete_habits = $conn->prepare("DELETE FROM habits WHERE user_id = ?");
$delete_habits->bind_param("i", $user_id);
$delete_habits->execute();
$delete_habits->close();

// Delete the user
$delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_user->bind_param("i", $user_id);
$delete_user->execute();
$delete_user->close();

$_SESSION['success'] = "User '" . htmlspecialchars($user['name']) . "' and all their habits have been deleted successfully!";
header("Location: admin_dashboard.php");
exit();

