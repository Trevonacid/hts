<?php
session_start();
include "db.php";

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Comprehensive Validation
    $errors = [];

    // Name validation
    if (empty($name)) {
        $errors[] = "Full name is required!";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long!";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name must not exceed 100 characters!";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/u", $name)) {
        $errors[] = "Name can only contain letters, spaces, hyphens, and apostrophes!";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email address is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address!";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email address must not exceed 100 characters!";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required!";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long!";
    } elseif (strlen($password) > 128) {
        $errors[] = "Password must not exceed 128 characters!";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter!";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter!";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number!";
    } elseif (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
        $errors[] = "Password must contain at least one special character!";
    }

    // Confirm password validation
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password!";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // If no validation errors, proceed with database checks
    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists! Please use a different email or try logging in.";
        } else {
            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($hashed_password === false) {
                $errors[] = "Password hashing failed. Please try again.";
            } else {
                // Insert user into database
                $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success = "Registration successful! Redirecting to login...";
                    header("refresh:2;url=login.php");
                } else {
                    $errors[] = "Registration failed. Please try again later.";
                }
            }
        }
        $stmt->close();
    }
    
    // Display first error if any
    if (!empty($errors)) {
        $error = $errors[0];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Habit Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Create Account</h1>
            <p class="subtitle">Start tracking your habits today</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required minlength="2" maxlength="100" pattern="[a-zA-Z\s'-]+">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password (min. 8 characters)" required minlength="8" maxlength="128">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                    <small class="form-hint">Must contain: uppercase, lowercase, number, and special character</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </form>
            
            <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
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

        // Client-side validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.auth-form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const name = document.getElementById('name');
            const email = document.getElementById('email');

            // Real-time password validation
            password.addEventListener('input', function() {
                validatePassword(this);
            });

            // Real-time confirm password validation
            confirmPassword.addEventListener('input', function() {
                if (this.value && password.value) {
                    if (this.value !== password.value) {
                        this.setCustomValidity('Passwords do not match!');
                    } else {
                        this.setCustomValidity('');
                    }
                }
            });

            // Name validation
            name.addEventListener('input', function() {
                const value = this.value.trim();
                if (value.length > 0 && value.length < 2) {
                    this.setCustomValidity('Name must be at least 2 characters long!');
                } else if (!/^[a-zA-Z\s'-]+$/.test(value) && value.length > 0) {
                    this.setCustomValidity('Name can only contain letters, spaces, hyphens, and apostrophes!');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Email validation
            email.addEventListener('input', function() {
                if (this.value && !this.validity.valid) {
                    this.setCustomValidity('Please enter a valid email address!');
                } else {
                    this.setCustomValidity('');
                }
            });

            function validatePassword(field) {
                const value = field.value;
                let message = '';

                if (value.length > 0 && value.length < 8) {
                    message = 'Password must be at least 8 characters long!';
                } else if (!/[A-Z]/.test(value) && value.length > 0) {
                    message = 'Password must contain at least one uppercase letter!';
                } else if (!/[a-z]/.test(value) && value.length > 0) {
                    message = 'Password must contain at least one lowercase letter!';
                } else if (!/[0-9]/.test(value) && value.length > 0) {
                    message = 'Password must contain at least one number!';
                } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(value) && value.length > 0) {
                    message = 'Password must contain at least one special character!';
                }

                field.setCustomValidity(message);
            }

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = [];

                // Validate all fields
                if (!name.value.trim() || name.value.trim().length < 2) {
                    errors.push('Please enter a valid name (at least 2 characters)');
                    isValid = false;
                }

                if (!email.value || !email.validity.valid) {
                    errors.push('Please enter a valid email address');
                    isValid = false;
                }

                if (!password.value || password.value.length < 8) {
                    errors.push('Password must be at least 8 characters long');
                    isValid = false;
                }

                if (password.value !== confirmPassword.value) {
                    errors.push('Passwords do not match');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    // The browser's built-in validation will show the errors
                }
            });
        });
    </script>
</body>
</html>
