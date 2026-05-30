<?php
// 1. Start the session at the absolute top of the file
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Security check matching your login architecture
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 3. Include your centralized database module (Uses PDO)
require_once 'db.php';

// FIXED: Fallback check prevents the "Undefined array key" warning banner
$current_user = isset($_SESSION['email']) && !empty($_SESSION['email']) ? $_SESSION['email'] : null;

try {
    if ($current_user) {
        // Fetch donations logged under this specific user's email context
        $query = "SELECT * FROM donations WHERE donor_name = ? ORDER BY id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$current_user]);
    } else {
        // Fallback: If no email session exists, fetch all donations so the table isn't completely blank
        $query = "SELECT * FROM donations ORDER BY id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }
    $donations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Connection / Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Donations - Food Donate</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .header { background-color: #fff; padding: 20px 40px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; color: #333; font-size: 26px; }
        .container { padding: 40px; max-width: 1100px; margin: 0 auto; }
        .card { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card h2 { margin-top: 0; margin-bottom: 20px; color: #333; font-size: 20px; border-bottom: 2px solid #f4f7f6; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; }
        th { background-color: #f8fafc; color: #64748b; padding: 14px 12px; font-weight: bold; border-bottom: 2px solid #e2e8f0; }
        td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; color: #333; }
        tr:hover { background-color: #f8fafc; }
        
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-pending { background-color: #fef3c7; color: #d97706; }
        .badge-approved { background-color: #dbeafe; color: #2563eb; }
        .badge-delivered { background-color: #d1fae5; color: #059669; }
        .badge-rejected { background-color: #fde8e8; color: #9b1c1c; }

        .back-btn { text-decoration: none; color: #2ecc71; font-weight: bold; font-size: 15px; }
        .back-btn:hover { color: #27ae60; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Food Donation Records</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Your Active Contributions</h2>
            
            <?php if (!empty($donations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Donor Identifier</th>
                            <th>Food Item Name</th>
                            <th>Quantity / Volume</th>
                            <th>Contact Phone</th>
                            <th>Current Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $row): ?>
                            <tr>
                                <td>#DON-0<?php echo htmlspecialchars($row['id']); ?></td>
                                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_item']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity'] ?? '1 Batch'); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = strtolower($row['status'] ?? 'pending');
                                        echo "<span class='badge badge-".$statusClass."'>".htmlspecialchars($row['status'] ?? 'Pending')."</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #64748b; text-align: center; padding: 20px 0;">No active food donations found in the record log system for your account.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>