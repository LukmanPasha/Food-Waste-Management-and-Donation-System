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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Dimmed translucent linear layer over a vibrant food sharing theme background */
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Frosted Glassmorphism Login Container Card */
        .login-container {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 50px 35px;
            width: 100%;
            max-width: 420px;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
            text-align: center;
        }

        h2 {
            font-size: 2.3rem;
            margin-bottom: 35px;
            font-weight: 700;
            /* Colorful bright orange-to-emerald gradient headline text */
            background: linear-gradient(135deg, #ff9f43, #00b894);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.85rem;
            color: #55efc4; /* Colorful mint neon labels */
            font-weight: 600;
            margin-bottom: 6px;
            padding-left: 2px;
            letter-spacing: 0.3px;
        }

        /* Translucent sleek form input fields */
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        input:focus {
            border-color: #ff9f43; /* Changes to glowing orange on focus active state */
            background: rgba(0, 0, 0, 0.45);
            box-shadow: 0 0 14px rgba(255, 159, 67, 0.3);
        }

        /* Solid Color Action Button with vibrant orange-to-coral background gradient */
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff9f43, #ff5252);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 12px;
            box-shadow: 0 5px 15px rgba(255, 82, 82, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 82, 82, 0.55);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Warning error notice banner box */
        .error {
            color: #ff5252;
            background: rgba(255, 82, 82, 0.15);
            border-left: 4px solid #ff5252;
            padding: 12px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0 20px 0;
            color: #cbd5e1;
            font-size: 0.85rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }

        .btn-signin {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px;
            background-color: transparent;
            border: 2px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.2s;
        }

        .btn-signin:hover {
            background-color: #ffffff;
            color: #1e293b;
            border-color: #ffffff;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>System Login</h2>
    
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

        <button type="submit" name="login_btn" class="btn-register">Sign In</button>
    </form>

    <div class="divider">New to the platform?</div>
    <a href="signup.php" class="btn-signin">Create Account</a>
</div>

</body>
</html>