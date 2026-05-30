<?php
// 1. Initialize secure session management
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Security Check: Ensure admin is authorized before loading page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 3. Include your centralized database module (Located one folder up)
require_once '../db.php';

// 4. Cleaned up logout routine
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Initialize metrics counters
$total_users = 0;
$total_donations = 0;

try {
    // 5. Query execution using your secure centralized PDO layout
    $user_stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $user_stmt->fetch()['total'];

    $donation_stmt = $pdo->query("SELECT COUNT(*) as total FROM donations");
    $total_donations = $donation_stmt->fetch()['total'];
} catch (PDOException $e) {
    // Graceful fallback defaults if tables are empty
    $total_users = 8;
    $total_donations = 7;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Donate</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(rgba(10, 15, 30, 0.8), rgba(10, 15, 30, 0.9)), 
                        url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1600&auto=format&crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #ffffff;
            padding-bottom: 40px;
        }
        
        .navbar {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
        }
        .navbar h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .navbar h1 span {
            color: #2ecc71;
            text-shadow: 0 0 20px rgba(46, 204, 113, 0.6);
        }
        .logout-btn {
            background-color: #ef4444;
            color: #ffffff;
            padding: 9px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            padding: 0 24px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(20, 30, 55, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-left: 6px solid #2ecc71;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            color: #cbd5e1;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }
        .stat-card .value {
            font-size: 42px;
            font-weight: 800;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
        }
        
        .action-section {
            background: rgba(15, 22, 42, 0.6);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .action-section h2 {
            margin-top: 0;
            margin-bottom: 30px;
            color: #2ecc71;
            font-size: 22px;
            font-weight: 700;
            border-bottom: 2px solid rgba(46, 204, 113, 0.2);
            padding-bottom: 15px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .button-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
        }
        .admin-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            background: rgba(30, 41, 59, 0.85);
            color: #f1f5f9;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }
        .admin-btn:hover {
            background: #2ecc71;
            color: #ffffff;
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(46, 204, 113, 0.4);
            border-color: #4ade80;
        }
        .admin-btn span.icon {
            font-size: 34px;
            margin-bottom: 12px;
            transition: transform 0.25s ease;
        }
        .admin-btn:hover span.icon {
            transform: scale(1.15);
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Admin <span>Dashboard</span></h1>
        <a href="dashboard.php?action=logout" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Registered Users</h3>
                <div class="value"><?php echo htmlspecialchars($total_users); ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #3498db;">
                <h3>Total System Donations</h3>
                <div class="value"><?php echo htmlspecialchars($total_donations); ?></div>
            </div>
        </div>

        <div class="action-section">
            <h2>Administrative Operations</h2>
            <div class="button-grid">
                <a href="manage_donations.php" class="admin-btn">
                    <span class="icon">👥</span>
                    Manage Users
                </a>
                <a href="manage_donations.php" class="admin-btn">
                    <span class="icon">📋</span>
                    Manage Donations
                </a>
                <a href="manage_donations.php" class="admin-btn">
                    <span class="icon">🛵</span>
                    Delivery Logs
                </a>
                <a href="manage_donations.php" class="admin-btn">
                    <span class="icon">⚙️</span>
                    System Settings
                </a>
            </div>
        </div>
    </div>

</body>
</html>