<?php
$host = 'localhost';
$db   = 'habit_tracker';
$user = 'root'; // default XAMPP username
$pass = 'bimal123';     // default XAMPP password is empty

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>