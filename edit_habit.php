<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$habit = null;

// Get habit ID
$habit_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Verify that the habit belongs to the user and fetch it
if ($habit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $habit = $result->fetch_assoc();
    $stmt->close();
    
    if (!$habit) {
        $_SESSION['error'] = "Habit not found or you don't have permission to edit it!";
        header("Location: dashboard.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid habit ID!";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if (isset($_POST['update'])) {
    $habit_name = trim($_POST['habit_name'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($habit_name)) {
        $errors[] = "Habit name cannot be empty!";
    } elseif (strlen($habit_name) < 2) {
        $errors[] = "Habit name must be at least 2 characters long!";
    } elseif (strlen($habit_name) > 100) {
        $errors[] = "Habit name must not exceed 100 characters!";
    } elseif (!preg_match("/^[a-zA-Z0-9\s\-_.,!?()]+$/u", $habit_name)) {
        $errors[] = "Habit name contains invalid characters!";
    }
    
    if (empty($errors)) {
        // Update the habit
        $stmt = $conn->prepare("UPDATE habits SET habit_name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $habit_name, $habit_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Habit updated successfully!";
            $stmt->close();
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to update habit. Please try again.";
        }
        $stmt->close();
    } else {
        $error = $errors[0];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Habit - Habit Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Edit Habit</h1>
            <p class="subtitle">Update your habit name</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="habit_name">Habit Name</label>
                    <input type="text" id="habit_name" name="habit_name" 
                           placeholder="Enter habit name" 
                           value="<?php echo htmlspecialchars($habit['habit_name'] ?? ''); ?>" 
                           required 
                           minlength="2" 
                           maxlength="100"
                           pattern="[a-zA-Z0-9\s\-_.,!?()]+">
                    <small class="form-hint">2-100 characters. Letters, numbers, spaces, and basic punctuation allowed.</small>
                </div>
                <div class="modal-actions" style="margin-top: 1.5rem; padding: 0;">
                    <a href="dashboard.php" class="btn btn-secondary" style="width: auto; text-decoration: none;">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary" style="width: auto;">Update Habit</button>
                </div>
            </form>
            
            <p class="auth-link"><a href="dashboard.php">← Back to Dashboard</a></p>
        </div>
    </div>
</body>
</html>

