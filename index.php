<?php
session_start();
// If user is already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Tracker - Build Better Habits</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <header class="site-navbar">
        <div class="site-navbar-inner">
            <a href="index.php" class="site-logo">Habit<span>Tracker</span></a>
            <input type="checkbox" id="nav-toggle" class="nav-toggle">
            <label for="nav-toggle" class="nav-toggle-label">
                <span></span>
            </label>
            <nav class="site-nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="admin_login.php">Admin</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="hero-section">
            <h1 class="hero-title">Habit Tracker</h1>
            <p class="hero-subtitle">Build better habits, one day at a time</p>
            <p class="hero-description">Track your daily habits, monitor your progress, and achieve your goals with our simple and intuitive habit tracking system.</p>
            
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary btn-large">Get Started</a>
                <a href="login.php" class="btn btn-secondary btn-large">Login</a>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <h3>Track Progress</h3>
                    <p>Monitor your daily habits and see your improvement over time</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-check-circle"></i></div>
                    <h3>Stay Consistent</h3>
                    <p>Build lasting habits with daily reminders and tracking</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-bullseye"></i></div>
                    <h3>Achieve Goals</h3>
                    <p>Set and accomplish your personal goals one habit at a time</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
