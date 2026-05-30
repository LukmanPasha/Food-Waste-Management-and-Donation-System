<?php
// 1. Initialize secure session management
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Authentication Protection (Fixed to match your active admin session)
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// 3. Establish Database Connection 
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "food_waste"; 

$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($connection->connect_error) {
    die("Database Connection Failed: " . $connection->connect_error);
}

$error_msg = "";
$success_msg = "";

// 4. Handle Form Submission for Adding a New Waste Log
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_log'])) {
    $item_name   = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
    $quantity_kg = isset($_POST['quantity_kg']) ? floatval($_POST['quantity_kg']) : 0;
    $reason      = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $logged_by   = $_SESSION['admin_username']; 

    if (!empty($item_name) && $quantity_kg > 0 && !empty($reason)) {
        $stmt = $connection->prepare("INSERT INTO waste_logs (item_name, quantity_kg, reason, logged_by) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdss", $item_name, $quantity_kg, $reason, $logged_by);
            if ($stmt->execute()) {
                $success_msg = "Waste log recorded successfully.";
            } else {
                $error_msg = "Failed to write record entry to database: " . $connection->error;
            }
            $stmt->close();
        }
    } else {
        $error_msg = "Please fill out all input fields with valid data values.";
    }
}

// 5. Query Active Waste Logs Data Records
$query = "SELECT * FROM waste_logs ORDER BY created_at DESC";
$result = $connection->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Logs - Admin Panel</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f1f5f9; display: flex; min-height: 100vh; }
        
        /* Sidebar Navigation Layout styling */
        .sidebar { width: 260px; background-color: #1e293b; color: #ffffff; padding: 25px 20px; display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; color: #2ecc71; text-align: center; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 6px; margin-bottom: 10px; font-weight: 500; transition: all 0.2s; }
        .sidebar a:hover { background-color: #334155; color: #ffffff; }
        .sidebar a.active { background-color: #334155; color: #ffffff; border-left: 4px solid #2ecc71; }
        .sidebar a.logout-btn { margin-top: auto; background-color: #ef4444; color: white; text-align: center; }
        .sidebar a.logout-btn:hover { background-color: #dc2626; }
        
        /* Main Panel Workspace */
        .main-content { flex: 1; margin-left: 260px; width: calc(100% - 260px); padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #0f172a; font-size: 28px; }
        .welcome-badge { background-color: #def7ec; color: #03543f; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; text-transform: capitalize; }
        
        /* Grid Split Layout for Form vs Table */
        .workspace-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; height: fit-content; }
        .card h3 { color: #1e293b; font-size: 18px; margin-bottom: 20px; border-left: 3px solid #2ecc71; padding-left: 10px; }
        
        /* Form inputs design configuration */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; color: #475569; font-weight: 600; font-size: 14px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px; outline: none; }
        input:focus, select:focus { border-color: #2ecc71; }
        .btn-submit { width: 100%; padding: 11px; background-color: #1e293b; border: none; color: white; font-weight: bold; font-size: 14px; border-radius: 5px; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background-color: #334155; }
        
        /* Alerts */
        .alert { padding: 10px; border-radius: 5px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
        .alert-error { background-color: #fde8e8; color: #9b1c1c; }
        .alert-success { background-color: #def7ec; color: #03543f; }
        
        /* Data grid table system */
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { background-color: #f8fafc; color: #64748b; padding: 12px; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        tr:hover { background-color: #f8fafc; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Food Waste Admin</h2>
        <a href="dashboard.php">Overview Dashboard</a>
        <a href="donations.php">Food Donations</a>
        <a href="waste-logs.php" class="active">Waste Logs</a>
        <a href="centers.php">Distribution Centers</a>
        <a href="dashboard.php?action=logout" class="logout-btn">Log Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>System Waste Logs</h1>
            <div class="welcome-badge">
                Logged in: <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </div>
        </div>
        
        <div class="workspace-grid">
            <div class="card">
                <h3>Log Food Waste</h3>
                
                <?php if(!empty($error_msg)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>
                <?php if(!empty($success_msg)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php endif; ?>

                <form action="waste-logs.php" method="POST">
                    <input type="hidden" name="add_log" value="1">
                    
                    <div class="form-group">
                        <label for="item_name">Food Item Name</label>
                        <input type="text" id="item_name" name="item_name" placeholder="e.g., Veg Biryani" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity_kg">Loss Weight (in Kg)</label>
                        <input type="number" id="quantity_kg" name="quantity_kg" step="0.01" min="0.1" placeholder="e.g., 5.50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason for Disposal</label>
                        <select id="reason" name="reason" required>
                            <option value="" disabled selected>Select disposal factor</option>
                            <option value="Expired before distribution window">Expired/Spoiled</option>
                            <option value="Contaminated/Damaged packaging">Damaged Packaging</option>
                            <option value="Refused by recipient center">Center Refusal</option>
                            <option value="Logistical delays">Logistical Delay</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-submit">Submit Loss Entry</button>
                </form>
            </div>

            <div class="card">
                <h3>Tracked Waste Entries</h3>
                
                <?php if (isset($result) && $result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Log ID</th>
                                <th>Item Particulars</th>
                                <th>Quantity</th>
                                <th>Reason Code</th>
                                <th>Logged By</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#WL-0<?php echo $row['id']; ?></td>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td style="color: #ef4444; font-weight: 600;"><?php echo htmlspecialchars($row['quantity_kg']); ?> Kg</td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td style="text-transform: capitalize;"><?php echo htmlspecialchars($row['logged_by']); ?></td>
                                    <td style="font-size: 13px; color: #64748b;"><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #64748b;">No logged metrics recorded. Use the form component to report items.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
<?php 
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close(); 
}
?>