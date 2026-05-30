<?php
// 1. Initialize secure session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Protect the page: If the admin isn't logged in, redirect back to login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 3. Database Connection Setup
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "food_waste"; 

$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($connection->connect_error) {
    die("Database Connection Failed: " . $connection->connect_error);
}

// 4. Handle Status Update Actions (Approve / Deliver)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $new_status = "";
    if ($action === 'approve') { $new_status = "Approved"; }
    elseif ($action === 'deliver') { $new_status = "Delivered"; }
    
    if (!empty($new_status)) {
        $stmt = $connection->prepare("UPDATE donations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: donations.php"); // Refresh cleanly
        exit();
    }
}

// 5. Fetch live records from the database
$query = "SELECT * FROM donations ORDER BY created_at DESC";
$result = $connection->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Donations Management - Admin Panel</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f1f5f9; display: flex; min-height: 100vh; }
        
        /* Sidebar Navigation Layout */
        .sidebar { width: 260px; background-color: #1e293b; color: #ffffff; padding: 25px 20px; display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; color: #2ecc71; text-align: center; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 6px; margin-bottom: 10px; font-weight: 500; transition: all 0.2s; }
        .sidebar a:hover { background-color: #334155; color: #ffffff; }
        .sidebar a.active { background-color: #334155; color: #ffffff; border-left: 4px solid #2ecc71; }
        .sidebar a.logout-btn { margin-top: auto; background-color: #ef4444; color: white; text-align: center; }
        .sidebar a.logout-btn:hover { background-color: #dc2626; }
        
        /* Main Workspace Content */
        .main-content { flex-1: 1; margin-left: 260px; width: calc(100% - 260px); padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #0f172a; font-size: 28px; }
        .welcome-badge { background-color: #def7ec; color: #03543f; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; text-transform: capitalize; }
        
        /* Interactive Data Table Layout */
        .card { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .card h3 { color: #1e293b; font-size: 18px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 15px; }
        th { background-color: #f8fafc; color: #64748b; padding: 14px; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        td { padding: 14px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        tr:hover { background-color: #f8fafc; }
        
        /* Dynamic Status Badges */
        .badge { padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .badge-pending { background-color: #fef3c7; color: #d97706; }
        .badge-approved { background-color: #dbeafe; color: #2563eb; }
        .badge-delivered { background-color: #d1fae5; color: #059669; }
        
        /* Action Control Buttons */
        .btn-action { text-decoration: none; font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 4px; margin-right: 5px; transition: background 0.2s; }
        .btn-approve { background-color: #3b82f6; color: white; }
        .btn-approve:hover { background-color: #2563eb; }
        .btn-deliver { background-color: #10b981; color: white; }
        .btn-deliver:hover { background-color: #059669; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Food Waste Admin</h2>
        <a href="dashboard.php">Overview Dashboard</a>
        <a href="donations.php" class="active">Food Donations</a>
        <a href="waste-logs.php">Waste Logs</a>
        <a href="centers.php">Distribution Centers</a>
        <a href="dashboard.php?action=logout" class="logout-btn">Log Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Food Donations</h1>
            <div class="welcome-badge">
                Manager: <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </div>
        </div>
        
        <div class="card">
            <h3>Active Contribution Records</h3>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor Name</th>
                            <th>Food Item</th>
                            <th>Quantity</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_item']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = strtolower($row['status']);
                                        echo "<span class='badge badge-".$statusClass."'>".$row['status']."</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <a href="donations.php?action=approve&id=<?php echo $row['id']; ?>" class="btn-action btn-approve">Approve</a>
                                    <?php elseif ($row['status'] === 'Approved'): ?>
                                        <a href="donations.php?action=deliver&id=<?php echo $row['id']; ?>" class="btn-action btn-deliver">Mark Delivered</a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 13px;">No actions pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #64748b;">No active donation records found in the database. When users submit food items, they will instantly appear here.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>