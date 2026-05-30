<?php
// Start session for admin tracking
session_start();

// Database Connection Settings - Configured for your active database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "food_waste"; // Connects to your actual database visible in phpMyAdmin

// Connect to the database cleanly in a single error-safe step
$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);

// Error check connection gracefully instead of letting the application crash
if (!$conn) {
    die("<div class='alert alert-danger m-4' style='font-family: sans-serif; max-width: 600px; margin: 30px auto;'>
            <h4 class='fw-bold'>Database Connection Error!</h4>
            <p>Could not connect to the database engine. Please complete the following steps:</p>
            <ol>
                <li>Open your <strong>XAMPP Control Panel</strong> application.</li>
                <li>Locate the <strong>MySQL</strong> module row.</li>
                <li>Click the <strong>Start</strong> button next to it until it highlights green.</li>
            </ol>
            <small class='text-muted'>System Trace: " . mysqli_connect_error() . "</small>
         </div>");
}

// Fetch stats summary safely from your specific tables
$total_donations = 0;
$res_donations = mysqli_query($conn, "SELECT COUNT(*) as count FROM food_donations");
if ($res_donations) {
    $row = mysqli_fetch_assoc($res_donations);
    $total_donations = $row['count'];
}

$total_agents = 0;
$res_agents = mysqli_query($conn, "SELECT COUNT(*) as count FROM delivery_persons");
if ($res_agents) {
    $row = mysqli_fetch_assoc($res_agents);
    $total_agents = $row['count'];
}

$total_feedback = 0;
$res_feedback = mysqli_query($conn, "SELECT COUNT(*) as count FROM user_feedback");
if ($res_feedback) {
    $row = mysqli_fetch_assoc($res_feedback);
    $total_feedback = $row['count'];
}

// Fetch detailed items matching your table column mappings
$donations_list = mysqli_query($conn, "SELECT * FROM food_donations ORDER BY fid DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .admin-sidebar { background-color: #2c3e50; min-height: 100vh; color: white; }
        .sidebar-link { color: #ecf0f1; text-decoration: none; padding: 12px 20px; display: block; border-left: 4px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #34495e; color: #2ecc71; border-left-color: #2ecc71; }
        .stat-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .bg-gradient-green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .bg-gradient-blue { background: linear-gradient(135deg, #3498db, #2980b9); }
        .bg-gradient-orange { background: linear-gradient(135deg, #e67e22, #d35400); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-3 col-lg-2 px-0 admin-sidebar d-none d-md-block sticky-top">
            <div class="p-4 text-center border-bottom border-secondary">
                <h4 class="fw-bold mb-0 text-white">Food <span style="color:#2ecc71;">Donate</span></h4>
                <small class="text-muted text-uppercase tracking-wider">Management</small>
            </div>
            <div class="py-3">
                <a href="admin.php" class="sidebar-link active">Dashboard</a>
                <a href="manage_donations.php" class="sidebar-link">Donations</a>
                <a href="manage_agents.php" class="sidebar-link">Delivery Personnel</a>
                <a href="view_feedback.php" class="sidebar-link">Feedback Log</a>
                <div class="border-top border-secondary my-3"></div>
                <a href="../logout.php" class="sidebar-link text-danger">Exit System</a>
            </div>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 fw-bold text-dark">System Command Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-success px-3 py-2 fs-7">Database Connected: food_waste</span>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card stat-card bg-gradient-green text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-white-50 small mb-1">Total Food Shipments</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $total_donations; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card stat-card bg-gradient-blue text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-white-50 small mb-1">Active Couriers</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $total_agents; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card stat-card bg-gradient-orange text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-white-50 small mb-1">Feedback Submissions</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $total_feedback; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0">Recent Distribution Listings</h5>
                    <a href="manage_donations.php" class="btn btn-sm btn-outline-secondary px-3">View Full Directory</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Listing ID</th>
                                <th>Item Particulars</th>
                                <th>Quantity Metric</th>
                                <th>Location Reference</th>
                                <th>Action Panel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($donations_list && mysqli_num_rows($donations_list) > 0) {
                                while($row = mysqli_fetch_assoc($donations_list)) {
                                    echo "<tr>";
                                    echo "<td class='fw-bold'>#" . htmlspecialchars($row['fid'] ?? $row['id'] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['food'] ?? $row['food_item'] ?? 'Food Package') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['quantity'] ?? 'Not Specified') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['location'] ?? $row['address'] ?? 'General') . "</td>";
                                    echo "<td><a href='edit_donation.php?id=".(urlencode($row['fid'] ?? $row['id'] ?? ''))."' class='btn btn-sm btn-light border text-primary fw-medium px-2 py-1'>Manage</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-muted small'>No distribution entries currently listed in database registry.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the open database connection gracefully
mysqli_close($conn);
?>