<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your database connection file from the parent folder
include('../connection.php');

$error_message = "";

// 2. Handle Admin Login Verification
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    
    // Querying your admin table layout
    $sql = "SELECT * FROM admin WHERE username = ? AND password = ?";
    if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $admin_data = mysqli_fetch_assoc($result);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_data['username'];
            
            // Redirect straight to your analytics dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid administrative username or password.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Database Query Error: " . mysqli_error($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); width: 100%; max-width: 380px; box-sizing: border-box; }
        h2 { margin-top: 0; color: #212529; text-align: center; border-bottom: 2px solid #20c997; padding-bottom: 10px; }
        label { font-weight: bold; display: block; margin-top: 15px; margin-bottom: 5px; color: #495057; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; font-size: 1em; }
        .btn-login { background-color: #20c997; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 25px; font-size: 1em; }
        .btn-login:hover { background-color: #1aa179; }
        .alert-error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; border: 1px solid #f5c6cb; font-size: 0.9em; margin-bottom: 15px; text-align: center; }
        .fallback-link { display: block; text-align: center; margin-top: 15px; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🛡️ Admin Login Portal</h2>
    
    <?php if(!empty($error_message)): ?>
        <div class="alert-error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="username">Admin Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter admin username" required>

        <label for="password">Security Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>

        <button type="submit" name="login" class="btn-login">Secure Login</button>
    </form>
    
    <a href="dashboard.php" class="fallback-link" style="color: #6c757d; text-decoration: none;">Bypass straight to Dashboard →</a>
</div>

</body>
</html>