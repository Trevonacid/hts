<?php
// Run this file once to create the admin user
// After running, delete this file for security

include "db.php";

// Check if admin column exists, if not add it
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
    echo "Added is_admin column to users table.<br>";
}

// Check if admin already exists
$check_admin = $conn->prepare("SELECT id FROM users WHERE email = 'admin@admin.com'");
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists!<br>";
    echo "Email: admin@admin.com<br>";
    echo "Password: Admin@123<br>";
} else {
    // Create admin user
    $admin_name = "Admin";
    $admin_email = "admin@admin.com";
    $admin_password = password_hash("Admin@123", PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $admin_name, $admin_email, $admin_password);
    
    if ($stmt->execute()) {
        echo "<h2>Admin user created successfully!</h2>";
        echo "<p><strong>Email:</strong> admin@admin.com</p>";
        echo "<p><strong>Password:</strong> Admin@123</p>";
        echo "<p style='color: red;'><strong>IMPORTANT:</strong> Please change this password after first login!</p>";
        echo "<p><a href='admin_login.php'>Go to Admin Login</a></p>";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

