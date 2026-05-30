<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Database Connection Setup
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "food_waste"; 

$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($connection->connect_error) {
    die("Database Connection Failed: " . $connection->connect_error);
}

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $address  = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city     = isset($_POST['city']) ? trim($_POST['city']) : '';

    if (!empty($fullname) && !empty($email) && !empty($password) && !empty($address) && !empty($city)) {
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Inserts data safely into your updated users table
        $stmt = $connection->prepare("INSERT INTO users (name, email, password, address, city) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssss", $fullname, $email, $hashed_password, $address, $city);
            
            if ($stmt->execute()) {
                $success_msg = "Account created successfully! You can now log in.";
            } else {
                if ($connection->errno == 1062) {
                    $error_msg = "This email address is already registered.";
                } else {
                    $error_msg = "Database Error: " . $connection->error;
                }
            }
            $stmt->close();
        } else {
            $error_msg = "Database statement preparation failed: " . $connection->error;
        }
    } else {
        $error_msg = "All fields marked with an asterisk (*) are required.";
    }
}
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Register - Food Donate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Dimmed translucent background shield layer over a beautiful food background image */
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1600&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* Frosted Glassmorphism Container Card */
        .login-container {
            background: rgba(255, 255, 255, 0.11);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 45px 35px;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            font-weight: 700;
            /* Vibrant emerald-green to mint-teal gradient text decoration */
            background: linear-gradient(135deg, #00b894, #55efc4);
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
            color: #00b894;
            font-weight: 600;
            margin-bottom: 6px;
            padding-left: 2px;
        }

        /* Continuous translucent field borders */
        input[type="text"], input[type="email"], input[type="password"], textarea, select {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        input:focus, textarea:focus, select:focus {
            border-color: #55efc4;
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 12px rgba(85, 239, 196, 0.2);
        }

        textarea {
            resize: vertical;
            height: 75px;
        }

        /* Ensures proper contrast inside selector option panels */
        select option {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2ed573, #1abc9c);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(46, 213, 115, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 213, 115, 0.55);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Aesthetic contextual custom notice panels */
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

        .success {
            color: #2ed573;
            background: rgba(46, 213, 115, 0.15);
            border-left: 4px solid #2ed573;
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
            color: #b2bec3;
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
            border: 2px solid rgba(255, 255, 255, 0.4);
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
    <h2>Register Account</h2>
    
    <?php if (!empty($error_msg)): ?>
        <div class="error">⚠️ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class="success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="fullname">Name</label>
            <input type="text" id="fullname" name="fullname" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" required></textarea>
        </div>

        <div class="form-group">
            <label for="city">City (Karnataka)</label>
            <select id="city" name="city" required>
                <option value="" disabled selected>Select your city</option>
                <option value="Ramanagara">Ramanagara</option>
                <option value="Bengaluru">Bengaluru</option>
                <option value="Hubballi-Dharwad">Hubballi-Dharwad</option>
                <option value="Mysuru">Mysuru</option>
                <option value="Mangaluru">Mangaluru</option>
                <option value="Belagavi">Belagavi</option>
                <option value="Kalaburagi">Kalaburagi</option>
                <option value="Udupi">Udupi</option>
            </select>
        </div>

        <button type="submit" class="btn-register">Register</button>
    </form>

    <div class="divider">Already a member?</div>
    <a href="login.php" class="btn-signin">Sign In</a>
</div>

</body>
</html>