<?php
// 1. Initialize secure session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Protect the page: If the admin isn't logged in, redirect them back to login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 3. Handle Logout Request
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
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution Centers - Admin Panel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f1f5f9;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar Navigation Layout */
        .sidebar {
            width: 260px;
            background-color: #1e293b;
            color: #ffffff;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }
        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            color: #2ecc71;
            text-align: center;
        }
        .sidebar a {
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar a:hover {
            background-color: #334155;
            color: #ffffff;
        }
        .sidebar a.active {
            background-color: #334155;
            color: #ffffff;
            border-left: 4px solid #2ecc71;
        }
        .sidebar a.logout-btn {
            margin-top: auto;
            background-color: #ef4444;
            color: white;
            text-align: center;
        }
        .sidebar a.logout-btn:hover {
            background-color: #dc2626;
        }
        /* Main Workspace Panel */
        .main-content {
            flex-1: 1;
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0f172a;
            font-size: 28px;
        }
        .welcome-badge {
            background-color: #def7ec;
            color: #03543f;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .card h3 {
            color: #1e293b;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .card p {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Food Waste Admin</h2>
        <a href="dashboard.php">Overview Dashboard</a>
        <a href="donations.php">Food Donations</a>
        <a href="waste-logs.php">Waste Logs</a>
        <a href="centers.php" class="active">Distribution Centers</a>
        <a href="dashboard.php?action=logout" class="logout-btn">Log Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Distribution Centers</h1>
            <div class="welcome-badge">
                Active Admin: <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </div>
        </div>
        
        <div class="card">
            <h3>Regional Management Hub</h3>
            <p>The operational logistics routing interface is active. This workspace maps distribution facilities, tracks warehouse storage limits, and schedules drop-offs for registered community partners across regional parameters.</p>
        </div>
    </div>

</body>
</html>