<?php
// Save this file as: admin/register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your centralized database module
require_once '../db.php';

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        try {
            // Check if the email is already registered in the admins table
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error_msg = "An administrator account with that email already exists.";
            } else {
                // Securely hash the password before saving to the database
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // Insert the new admin account
                $insert_stmt = $pdo->prepare("INSERT INTO admins (fullname, email, password) VALUES (?, ?, ?)");
                if ($insert_stmt->execute([$fullname, $email, $hashed_password])) {
                    $success_msg = "Account created successfully! You can now log in.";
                } else {
                    $error_msg = "Something went wrong. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error_msg = "Please fill in all the required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), 
                        url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1600&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            display: flex; justify-content: center; align-items: center; height: 100vh;
        }
        .register-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px; border-radius: 12px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            width: 100%; max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h2 { text-align: center; color: #1e293b; margin-bottom: 6px; font-size: 26px; }
        p.subtitle {
            text-align: center; color: #e67e22; font-size: 13px; font-weight: 700;
            text-transform: uppercase; margin-bottom: 25px; letter-spacing: 1.5px;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 7px; color: #475569; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px;
            font-size: 15px; outline: none; background-color: #f8fafc; transition: all 0.3s ease;
        }
        input:focus {
            border-color: #2ecc71; background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
        }
        .btn-register {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none; color: white; font-size: 16px; font-weight: bold;
            border-radius: 6px; cursor: pointer; margin-top: 10px;
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3); transition: all 0.2s ease;
        }
        .btn-register:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(46, 204, 113, 0.4); }
        .error-banner {
            background-color: #fde8e8; color: #9b1c1c; border: 1px solid #f8b4b4;
            padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;
        }
        .success-banner {
            background-color: #def7ec; color: #03543f; border: 1px solid #bfgh56;
            padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center;
        }
        .login-link { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
        .login-link a { color: #27ae60; text-decoration: none; font-weight: 700; }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Admin Register</h2>
    <p class="subtitle">Food Waste Management System</p>

    <?php if (!empty($error_msg)): ?>
        <div class="error-banner"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class="success-banner"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="email">Official Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your official email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Security Password</label>
            <input type="password" id="password" name="password" placeholder="Create a secure password" required>
        </div>
        
        <button type="submit" class="btn-register">Register Account</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

</body>
</html>