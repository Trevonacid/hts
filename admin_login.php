<?php
session_start();
include "db.php";

// Redirect if already logged in as admin
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if (isset($_POST['admin_login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email address is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address!";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required!";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ? AND is_admin = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $stmt->close();
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials or you don't have admin access!";
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
    <title>Admin Login - Habit Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Admin Login</h1>
            <p class="subtitle">Administrator access only</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter admin email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter admin password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary">Login as Admin</button>
            </form>
            
            <p class="auth-link"><a href="index.php">← Back to Home</a></p>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('.eye-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>

