<?php
// 1. Start the session at the absolute top of the file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Security Check: Redirect to login if user session does not exist
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 3. Include your centralized database module (Moves up one folder to find db.php)
require_once '../db.php';

// Handle Action Updates (Approve / Reject / Delete) if a request is made via URL parameters
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action_id = intval($_GET['id']);
    $action_type = $_GET['action'];
    
    try {
        if ($action_type === 'delete') {
            $update_stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
            $update_stmt->execute([$action_id]);
        } elseif (in_array($action_type, ['approved', 'rejected', 'delivered'])) {
            $update_stmt = $pdo->prepare("UPDATE donations SET status = ? WHERE id = ?");
            $update_stmt->execute([ucfirst($action_type), $action_id]);
        }
        // Refresh the page to show clean updated results
        header("Location: manage_donations.php");
        exit();
    } catch (PDOException $e) {
        $error_msg = "Action failed: " . $e->getMessage();
    }
}

try {
    // 4. Query all active records from the shared table using PDO
    $query = "SELECT * FROM donations ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $all_donations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Connection / Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Donations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            border-bottom: 2px solid #f4f7f6;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 15px;
        }
        th {
            background-color: #2ecc71;
            color: white;
            padding: 14px 12px;
            font-weight: bold;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #333;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-approve { background-color: #d1fae5; color: #059669; }
        .btn-reject { background-color: #fde8e8; color: #9b1c1c; }
        .btn-delete { background-color: #e2e8f0; color: #475569; }
        
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-pending { background-color: #fef3c7; color: #d97706; }
        .badge-approved { background-color: #dbeafe; color: #2563eb; }
        .badge-delivered { background-color: #d1fae5; color: #059669; }
        .badge-rejected { background-color: #fde8e8; color: #9b1c1c; }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2ecc71;
            font-size: 15px;
            font-weight: bold;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 Manage Food Donations</h2>
    
    <?php if (!empty($all_donations)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Food Item</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_donations as $row): ?>
                    <tr>
                        <td>#DON-0<?php echo htmlspecialchars($row['id']); ?></td>
                        <td style="font-weight: bold;"><?php echo htmlspecialchars($row['donor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['food_item']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity'] ?? '1 Batch'); ?></td>
                        <td>
                            <?php 
                                $statusClass = strtolower($row['status'] ?? 'pending');
                                echo "<span class='badge badge-".$statusClass."'>".htmlspecialchars($row['status'] ?? 'Pending')."</span>";
                            ?>
                        </td>
                        <td>
                            <a href="manage_donations.php?action=approved&id=<?php echo $row['id']; ?>" class="action-btn btn-approve">Approve</a>
                            <a href="manage_donations.php?action=rejected&id=<?php echo $row['id']; ?>" class="action-btn btn-reject">Reject</a>
                            <a href="manage_donations.php?action=delete&id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #64748b; text-align: center; padding: 30px 0; font-style: italic;">
            No donations found in records.
        </p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">← Back to Admin Home</a>
</div>

</body>
</html>