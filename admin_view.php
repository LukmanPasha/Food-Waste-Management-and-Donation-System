<?php
// 1. Initialize secure session management
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Security Check: Ensure a valid administrator session is active
if (!isset($_SESSION['user_id'])) {
    header("Location: admin/login.php");
    exit();
}

// 3. Include your centralized database module
require_once 'db.php';

// 4. FIXED LOGOUT HANDLING: Clears sessions and forces redirect to the Admin Login Panel
if (isset($_GET['action']) && $_GET['action'] === 'admin_logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: admin/login.php");
    exit();
}

try {
    // 5. Fetch donations tracking record matching your interface headers
    $query = "SELECT id, food_item, phone, status FROM donations ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $donations = $stmt->fetchAll();
} catch (PDOException $e) {
    $donations = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View - Food Donations</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(rgba(10, 15, 30, 0.8), rgba(10, 15, 30, 0.9)), 
                        url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1600&auto=format&fit=crop') no-repeat center center fixed;
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
        .navbar h1 { font-size: 24px; font-weight: 800; color: #ffffff; }
        .navbar h1 span { color: #2ecc71; }
        
        .logout-btn {
            background-color: #ef4444;
            color: #ffffff;
            padding: 9px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .logout-btn:hover { background-color: #dc2626; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 24px; }
        
        .action-section {
            background: rgba(15, 22, 42, 0.6);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .action-section h2 { color: #2ecc71; font-size: 26px; margin-bottom: 8px; }
        .subtitle { color: #cbd5e1; font-size: 14px; margin-bottom: 25px; display: block; }

        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; }
        th { background-color: rgba(30, 41, 59, 0.9); color: #2ecc71; padding: 16px 12px; font-weight: 700; border-bottom: 2px solid rgba(46, 204, 113, 0.3); }
        td { padding: 16px 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); color: #f1f5f9; }
        tr:hover { background-color: rgba(255, 255, 255, 0.05); }

        .no-data { text-align: center; padding: 40px 0; color: #94a3b8; font-style: italic; }
        .back-link { display: inline-block; margin-top: 20px; color: #2ecc71; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Admin Dashboard <span>Control Panel</span></h1>
        <a href="admin_view.php?action=admin_logout" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <div class="action-section">
            <h2>View Food Image Donations</h2>
            <span class="subtitle">Monitor real-time food tracking details submitted by system portal users.</span>
            
            <?php if (!empty($donations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>FOOD ITEM NAME</th>
                            <th>CONTACT PHONE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $row): ?>
                            <tr>
                                <td>#DON-0<?php echo htmlspecialchars($row['id']); ?></td>
                                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['food_item']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No Data Found</p>
                    <small>There are either no entries logged or the system could not match your exact food record table columns.</small>
                </div>
            <?php endif; ?>

            <a href="admin/dashboard.php" class="back-link">← Back to Admin Dashboard</a>
        </div>
    </div>

</body>
</html>