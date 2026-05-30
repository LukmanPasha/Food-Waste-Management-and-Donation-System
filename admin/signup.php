<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Database Connection Placeholder - Keeps your system operational
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "food_waste"; 

$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and clean administrative form post inputs safely
    $admin_name  = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : '';
    $admin_email = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '';
    $password    = isset($_POST['password']) ? trim($_POST['password']) : '';
    $address     = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city        = isset($_POST['city']) ? trim($_POST['city']) : '';

    if (!empty($admin_name) && !empty($admin_email) && !empty($password) && !empty($address) && !empty($city)) {
        // Your database prepared statement insertion code goes here
        // $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $error_msg = "All administrative fields marked with an asterisk (*) are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Food Donate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* RGBA Transparent Tint Overlay + Premium Food Texture Background Image */
            background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.75)), 
                        url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* Frosted Glassmorphism Portal Card using RGBA transparency layers */
        .admin-container {
            background: rgba(255, 255, 255, 0.11);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 45px 35px;
            width: 100%;
            max-width: 450px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 4px;
            /* Colorful vibrant gradient for main title text header */
            background: linear-gradient(135deg, #ff9f43, #ff5252);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .portal-badge {
            font-size: 0.85rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 2px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.85rem;
            color: #ff9f43; /* Bright custom solid orange label font text */
            font-weight: 600;
            margin-bottom: 6px;
            padding-left: 2px;
            letter-spacing: 0.3px;
        }

        /* Sleek Translucent Form Controls via explicit RGBA styling formulas */
        input[type="text"], input[type="email"], input[type="password"], textarea, select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        input::placeholder, textarea::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        input:focus, textarea:focus, select:focus {
            border-color: #ff9f43;
            background: rgba(0, 0, 0, 0.45);
            box-shadow: 0 0 14px rgba(255, 159, 67, 0.3);
        }

        textarea {
            resize: vertical;
            height: 75px;
        }

        /* Ensures drop selector panel menu lists contrast legibly against the glass */
        select option {
            background-color: #111827;
            color: #ffffff;
        }

        /* Solid Color Action Accent Button with vibrant gradient background */
        .btn-admin-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff9f43, #ff5252);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(255, 82, 82, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 82, 82, 0.55);
        }

        .btn-admin-submit:active {
            transform: translateY(0);
        }

        /* Context notices with translucent styling attributes */
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
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }

        .btn-login-route {
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

        .btn-login-route:hover {
            background-color: #ffffff;
            color: #0f172a;
            border-color: #ffffff;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>Register Account</h2>
    <span class="portal-badge">Administrative Portal</span>
    
    <?php if (!empty($error_msg)): ?>
        <div class="error">⚠️ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="admin_name">Admin Full Name *</label>
            <input type="text" id="admin_name" name="admin_name" placeholder="e.g., Lukman Pasha" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="admin_email">Official Email Address *</label>
            <input type="email" id="admin_email" name="admin_email" placeholder="e.g., admin@fooddonate.com" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Secure Password *</label>
            <input type="password" id="password" name="password" placeholder="Create complex password" required>
        </div>

        <div class="form-group">
            <label for="address">Office / Station Address *</label>
            <textarea id="address" name="address" placeholder="Enter full address detail parameters" required></textarea>
        </div>

        <div class="form-group">
            <label for="city">City Location (Karnataka) *</label>
            <select id="city" name="city" required>
                <option value="" disabled selected>Select active administrative city</option>
                <option value="Bengaluru">Bengaluru</option>
                <option value="Hubballi-Dharwad">Hubballi-Dharwad</option>
                <option value="Mysuru">Mysuru</option>
                <option value="Mangaluru">Mangaluru</option>
                <option value="Belagavi">Belagavi</option>
            </select>
        </div>

        <button type="submit" class="btn-admin-submit">Create Admin Account</button>
    </form>

    <div class="divider">Already registered?</div>
    <a href="login.php" class="btn-login-route">Admin Login</a>
</div>

</body>
</html>