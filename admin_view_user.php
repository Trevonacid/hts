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

// Get user information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: admin_dashboard.php");
    exit();
}

// Get user's habits
$habits_stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY created_at DESC");
$habits_stmt->bind_param("i", $user_id);
$habits_stmt->execute();
$habits_result = $habits_stmt->get_result();
$habits = $habits_result->fetch_all(MYSQLI_ASSOC);
$habits_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div>
                <h1>User Details</h1>
                <p class="subtitle">Viewing: <?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </header>

        <div class="card">
            <h2>User Information</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <div>
                    <strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?>
                </div>
                <div>
                    <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                </div>
                <div>
                    <strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?>
                </div>
                <div>
                    <strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>User's Habits (<?php echo count($habits); ?>)</h2>
            
            <?php if (empty($habits)): ?>
                <div class="empty-state">
                    <p>This user has no habits.</p>
                </div>
            <?php else: ?>
                <div class="habits-grid">
                    <?php foreach ($habits as $habit): ?>
                        <?php
                        // Get habit stats
                        $stats_stmt = $conn->prepare("SELECT COUNT(*) as total FROM habit_logs WHERE habit_id = ?");
                        $stats_stmt->bind_param("i", $habit['id']);
                        $stats_stmt->execute();
                        $stats = $stats_stmt->get_result()->fetch_assoc();
                        $stats_stmt->close();
                        ?>
                        <div class="habit-card">
                            <div class="habit-header">
                                <h3><?php echo htmlspecialchars($habit['habit_name']); ?></h3>
                                <a href="admin_delete_habit.php?habit_id=<?php echo $habit['id']; ?>&user_id=<?php echo $user_id; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Delete this habit?');">🗑️</a>
                            </div>
                            <div class="habit-stats">
                                <div class="stat-mini">
                                    <span class="stat-label-mini">Total Completions</span>
                                    <span class="stat-value"><?php echo $stats['total'] ?? 0; ?></span>
                                </div>
                            </div>
                            <div class="habit-date">
                                Created: <?php echo date('M d, Y', strtotime($habit['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <a href="admin_delete_user.php?id=<?php echo $user_id; ?>" 
               class="btn btn-primary" 
               style="background: var(--error-color); text-decoration: none; display: inline-block;"
               onclick="return confirm('Are you sure you want to delete this user and ALL their habits? This action cannot be undone!');">
                Delete User and All Habits
            </a>
        </div>
    </div>
</body>
</html>

