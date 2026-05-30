<?php
// Start session storage globally
session_start();

// Include the database link setup from login.php
require_once 'login.php';

if (isset($_POST['login_btn'])) {
    
    // Clean user inputs
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Catch empty fields early
    if (empty($email) || empty($password)) {
        header("Location: index.php?error=empty");
        exit();
    }

    // Match row by email directly (Note: 'email' column is lowercase)
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        
        // CRITICAL FIX: Match user's input against the uppercase PASSWORD hash column
        if (password_verify($password, $user_data['PASSWORD'])) {
            
            // Credentials verified! Assign session details
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_name'] = $user_data['NAME']; // Uppercase column name
            $_SESSION['user_email'] = $user_data['email'];

            // Route user smoothly to your existing dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Bad password
            header("Location: index.php?error=invalid");
            exit();
        }
    } else {
        // No email matching inside user rows
        header("Location: index.php?error=invalid");
        exit();
    }
} else {
    // If someone accesses signin.php directly, redirect them back
    header("Location: index.php");
    exit();
}
?>