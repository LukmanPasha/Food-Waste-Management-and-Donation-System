<?php
// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "food_waste"; // Change this to your exact database name

// Create connection using $connection consistently
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection works
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>