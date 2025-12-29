<?php
session_start();
include "db.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);

// Get all users with their habit counts
$users_query = "SELECT u.id, u.name, u.email, u.created_at, 
                COUNT(DISTINCT h.id) as habit_count,
                COUNT(DISTINCT hl.id) as log_count
                FROM users u
                LEFT JOIN habits h ON u.id = h.user_id
                LEFT JOIN habit_logs hl ON h.id = hl.habit_id
                WHERE u.is_admin = 0
                GROUP BY u.id
                ORDER BY u.created_at DESC";
$users_result = $conn->query($users_query);
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Get statistics
$total_users = count($users);
$total_habits_stmt = $conn->query("SELECT COUNT(*) as total FROM habits");
$total_habits = $total_habits_stmt->fetch_assoc()['total'];

$total_logs_stmt = $conn->query("SELECT COUNT(*) as total FROM habit_logs");
$total_logs = $total_logs_stmt->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Habit Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div>
                <h1>Admin Dashboard 👨‍💼</h1>
                <p class="subtitle">Welcome, <?php echo htmlspecialchars($admin_name); ?> - Manage users and habits</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="index.php" class="btn btn-secondary">View Site</a>
                <a href="admin_logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_habits; ?></div>
                <div class="stat-label">Total Habits</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_logs; ?></div>
                <div class="stat-label">Total Logs</div>
            </div>
        </div>

        <div class="card">
            <h2>All Users</h2>
            
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <p>No users found.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="background: var(--bg-color); border-bottom: 2px solid var(--border-color);">
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">ID</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Name</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Email</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Habits</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Logs</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Joined</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['habit_count']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['log_count']); ?></td>
                                    <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="admin_view_user.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-secondary btn-small" 
                                           style="text-decoration: none; margin-right: 0.5rem;">View</a>
                                        <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-secondary btn-small" 
                                           style="background: var(--error-color); text-decoration: none;"
                                           onclick="return confirm('Are you sure you want to delete this user and all their habits? This action cannot be undone!');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

