<?php
// Start session storage globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection Setup (Adjust parameters if needed)
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "food_waste"; 

$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($connection->connect_error) {
    die("Database Connection Failed: " . $connection->connect_error);
}

$error_msg = "";

// Check for redirection error flags from URL queries
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'empty') {
        $error_msg = "All fields are required.";
    } elseif ($_GET['error'] == 'invalid') {
        $error_msg = "Invalid email address or password.";
    }
}

if (isset($_POST['login_btn'])) {
    
    // Clean user inputs
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Catch empty fields early
    if (empty($email) || empty($password)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=empty");
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
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=invalid");
            exit();
        }
    } else {
        // No email matching inside user rows
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=invalid");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Food Waste Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            position: relative;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
            background-color: rgb(17, 17, 17);
        }

        /* Multiple Food Images Grid Background Layer */
        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            z-index: -2;
        }

        .bg-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Warm dimming overlay using RGBA */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.65));
            z-index: -1;
        }

        /* Translucent RGBA Glassmorphism Container Card */
        .login-container {
            background: rgba(255, 255, 255, 0.14); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 45px 35px;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        /* Colorful Styled Typography */
        h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: rgba(255, 121, 63, 1.0); /* Vibrant Neon Pumpkin Orange */
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .system-tag {
            display: block;
            font-size: 0.85rem;
            color: rgba(46, 213, 115, 1.0); /* Bright Vivid Eco Green */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 30px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 22px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.9rem;
            color: rgba(241, 242, 246, 1.0); /* Crisp Off-White */
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        /* Translucent RGBA Inputs */
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: rgba(255, 255, 255, 1.0);
            font-size: 0.95rem;
            font-weight: 500;
            outline: none;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.45);
        }

        input:focus {
            border-color: rgba(255, 121, 63, 1.0); /* Glowing Orange border on focus */
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 12px rgba(255, 121, 63, 0.35);
        }

        /* Solid Bright Orange-Red Action Button using RGBA elements */
        .btn-primary-action {
            width: 100%;
            padding: 15px;
            background-color: rgba(255, 121, 63, 1.0); /* Vivid Solid Orange */
            border: none;
            color: rgba(255, 255, 255, 1.0);
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 15px rgba(255, 121, 63, 0.35);
            transition: all 0.2s ease;
        }

        .btn-primary-action:hover {
            background-color: rgba(255, 107, 43, 1.0); /* Slightly darker pure orange */
            box-shadow: 0 8px 20px rgba(255, 121, 63, 0.5);
            transform: translateY(-1px);
        }

        .btn-primary-action:active {
            transform: translateY(0);
        }

        /* Error Notice Box modified with transparent RGBA background */
        .error {
            color: rgba(255, 76, 76, 1.0);
            background: rgba(255, 76, 76, 0.15);
            border: 1px solid rgba(255, 76, 76, 0.25);
            border-left: 5px solid rgba(255, 76, 76, 1.0);
            padding: 12px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0 20px 0;
            color: rgba(241, 242, 246, 0.6);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }

        /* Secondary Solid Green Navigation Button via RGBA */
        .btn-secondary-action {
            display: block;
            width: 100%;
            text-align: center;
            padding: 13px;
            background-color: rgba(46, 213, 115, 1.0); /* Vivid Solid Green */
            color: rgba(255, 255, 255, 1.0);
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 15px rgba(46, 213, 115, 0.25);
            transition: all 0.2s ease;
        }

        .btn-secondary-action:hover {
            background-color: rgba(38, 195, 102, 1.0);
            box-shadow: 0 8px 20px rgba(46, 213, 115, 0.4);
            transform: translateY(-1px);
        }

        .btn-secondary-action:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>

<div class="bg-grid">
    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=900&auto=format&fit=crop" class="bg-img" alt="Food 1">
    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=900&auto=format&fit=crop" class="bg-img" alt="Food 2">
    <img src="https://images.unsplash.com/photo-1484723091739-30a097e8f929?q=80&w=900&auto=format&fit=crop" class="bg-img" alt="Food 3">
    <img src="https://images.unsplash.com/photo-1482049016688-2d3e1b311543?q=80&w=900&auto=format&fit=crop" class="bg-img" alt="Food 4">
</div>
<div class="bg-overlay"></div>

<div class="login-container">
    <h2>System Login</h2>
    <span class="system-tag">Food Waste Management System</span>
    
    <?php if (!empty($error_msg)): ?>
        <div class="error">⚠️ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" name="login_btn" class="btn-primary-action">Sign In</button>
    </form>

    <div class="divider">New to the platform?</div>
    <a href="signup.php" class="btn-secondary-action">Create Account</a>
</div>

</body>
</html>