<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Security Check: Redirect to admin login if admin session does not exist
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
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

// QUERY FIXED: Pulls from your active users table using your exact uppercase database columns
$query = "SELECT id, NAME, email, address, city FROM users ORDER BY id DESC";
$result = $connection->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agents - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-size: 22px;
            margin: 0;
        }
        .navbar h1 span {
            color: #2ecc71;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .card h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #1e293b;
            font-size: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #64748b;
            padding: 14px 12px;
            font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .back-btn {
            text-decoration: none;
            color: #2ecc71;
            font-weight: bold;
            font-size: 15px;
        }
        .back-btn:hover {
            color: #27ae60;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Admin <span>Operations</span></h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Registered Users & Agents Ledger</h2>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Delivery Address</th>
                            <th>City Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#USR-0<?php echo $row['id']; ?></td>
                                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['NAME']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['city']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #64748b; text-align: center; padding: 20px 0;">No registered users found in the database table grid yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php $connection->close(); ?>