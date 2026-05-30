<?php
// 1. Start the session to check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Security Check: If the user is not logged in, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 3. FIXED: Fallback mechanism to prevent the "Undefined array key" warning banner
$user_identifier = isset($_SESSION['email']) ? $_SESSION['email'] : 'User'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Food Waste Management System</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1920') no-repeat center center fixed; 
            background-size: cover;
            margin: 0; 
            padding: 40px 20px; 
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        
        .dashboard-container { 
            width: 100%;
            max-width: 900px; 
            background: rgba(255, 255, 255, 0.15); 
            backdrop-filter: blur(10px);          
            -webkit-backdrop-filter: blur(10px);   
            padding: 30px; 
            border-radius: 16px; 
            border: 1px solid rgba(255, 255, 255, 0.25); 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); 
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); 
            padding-bottom: 20px; 
            margin-bottom: 20px; 
        }
        h1 { color: #ffffff; font-size: 24px; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        
        .logout-btn { 
            background-color: #dc3545; 
            color: white; 
            padding: 10px 18px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold; 
            transition: background 0.2s;
        }
        .logout-btn:hover { background-color: #bd2130; }
        
        .content-box { 
            background: rgba(255, 255, 255, 0.1); 
            padding: 20px; 
            border-radius: 8px; 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .content-box h3 { margin-top: 0; color: #ffffff; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .content-box p { color: #f8f9fa; font-size: 15px; }
        
        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-top: 25px; 
        }
        
        .menu-card { 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            background: rgba(255, 255, 255, 0.85); 
            border: 1px solid rgba(255, 255, 255, 0.4); 
            padding: 25px 20px; 
            border-radius: 8px; 
            text-decoration: none; 
            color: #2c3e50; 
            transition: all 0.25s ease-in-out; 
            text-align: center; 
            font-weight: bold; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        
        .menu-card:hover { 
            transform: translateY(-4px); 
            background: rgba(255, 255, 255, 1); 
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); 
            border-color: #2ecc71; 
            color: #2ecc71; 
        }
        .menu-card span { 
            font-size: 13px; 
            font-weight: normal; 
            color: #7f8c8d; 
            margin-top: 8px; 
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user_identifier); ?>!</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="content-box">
        <h3>Food Waste Management Panel</h3>
        <p>Select an option below to manage donations, view active demands, or access administrative settings.</p>
        
        <div class="menu-grid">
            <a href="create_donation.php" class="menu-card">
                🎁 Create Donation
                <span>Donate surplus food</span>
            </a>
            
            <a href="view_donation.php" class="menu-card">
                📋 View My Donations
                <span>Track your food status</span>
            </a>
            
            <a href="view_demands.php" class="menu-card">
                🔍 View Demands
                <span>See what needs food</span>
            </a>
            
            <a href="admin/login.php" class="menu-card">
                🛡️ Admin View
                <span>System administration</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>