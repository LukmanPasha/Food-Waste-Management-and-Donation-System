<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Security Check: Redirect to login if user session does not exist
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: index.php");
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

$success_msg = "";
$error_msg = "";

// Handle Action Updates (Accept or Reject Donations) safely via GET parameters
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $updated_status = "";
    if ($action === 'accept') {
        $updated_status = 'Approved';
    } elseif ($action === 'reject') {
        $updated_status = 'Rejected';
    } elseif ($action === 'complete') {
        $updated_status = 'Delivered';
    }

    if (!empty($updated_status)) {
        $stmt = $connection->prepare("UPDATE donations SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $updated_status, $id);
            if ($stmt->execute()) {
                $success_msg = "Donation status updated to '$updated_status' successfully.";
            } else {
                $error_msg = "Failed to update record: " . $connection->error;
            }
            $stmt->close();
        }
    }
}

// Query all active donation records across the platform to display for processing
$query = "SELECT * FROM donations ORDER BY created_at DESC";
$result = $connection->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Donations - Food Donate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #fff;
            padding: 20px 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 26px;
        }
        .container {
            padding: 40px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .card h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 20px;
            border-bottom: 2px solid #f4f7f6;
            padding-bottom: 10px;
        }
        
        /* Table UI Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 15px;
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
            color: #333;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        
        /* Badges styles */
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pending { background-color: #fef3c7; color: #d97706; }
        .badge-approved { background-color: #dbeafe; color: #2563eb; }
        .badge-delivered { background-color: #d1fae5; color: #059669; }
        .badge-rejected { background-color: #fde8e8; color: #9b1c1c; }

        /* Action Controls Buttons */
        .btn-action {
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 4px;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-accept { background-color: #2ecc71; color: white; }
        .btn-accept:hover { background-color: #27ae60; }
        .btn-reject { background-color: #e74c3c; color: white; }
        .btn-reject:hover { background-color: #c0392b; }
        .btn-complete { background-color: #3498db; color: white; }
        .btn-complete:hover { background-color: #2980b9; }

        /* Banner Messages */
        .msg { padding: 12px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: bold; }
        .msg-error { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #f8b4b4; }
        .msg-success { background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; }
        .back-btn { text-decoration: none; color: #3498db; font-weight: bold; font-size: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Process Food Donations</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if(!empty($error_msg)): ?>
            <div class="msg msg-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <?php if(!empty($success_msg)): ?>
            <div class="msg msg-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Active Management Queue</h2>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor Name</th>
                            <th>Food Item Particulars</th>
                            <th>Contact Phone</th>
                            <th>Pipeline Status</th>
                            <th>Management Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_item']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = strtolower($row['status']);
                                        echo "<span class='badge badge-".$statusClass."'>".$row['status']."</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <a href="process_donation.php?action=accept&id=<?php echo $row['id']; ?>" class="btn-action btn-accept">Approve</a>
                                        <a href="process_donation.php?action=reject&id=<?php echo $row['id']; ?>" class="btn-action btn-reject">Reject</a>
                                    <?php elseif ($row['status'] === 'Approved'): ?>
                                        <a href="process_donation.php?action=complete&id=<?php echo $row['id']; ?>" class="btn-action btn-complete">Mark Delivered</a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 13px;">Processing Complete</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #64748b; text-align: center; padding: 20px 0;">No batches submitted in the operational ledger queues yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php $connection->close(); ?>