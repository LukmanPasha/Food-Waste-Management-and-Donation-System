<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your database connection file
include('connection.php');

// 2. Fetch the listings from the food_donations table
$sql = "SELECT * FROM food_donations ORDER BY Fid DESC";
$result = mysqli_query($connection, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Food Requests & Demands</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header-section { margin-bottom: 25px; border-bottom: 2px solid #f1f3f5; padding-bottom: 15px; }
        .demand-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .demand-table th, .demand-table td { padding: 12px; border: 1px solid #dee2e6; text-align: left; font-size: 0.95em; }
        .demand-table th { background-color: #f1f3f5; font-weight: bold; color: #495057; }
        .badge { background-color: #e2f0d9; color: #385723; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85em; }
        .btn-back { display: inline-block; margin-top: 20px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-back:hover { text-decoration: underline; }
        .empty-state { text-align: center; padding: 40px; color: #6c757d; border: 1px dashed #dee2e6; border-radius: 6px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>Active Requests & Demands</h1>
        <p style="color: #6c757d; margin: 0;">Review available items and incoming supply requests from the community.</p>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="demand-table">
            <thead>
                <tr>
                    <th>Donor Name</th>
                    <th>Food Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Type</th>
                    <th>Address</th>
                    <th>Contact Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['food']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><span class="badge"><?php echo htmlspecialchars($row['quantity']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['phoneno']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <h3>No active demands found.</h3>
            <p>New food donation entries will populate here once submitted.</p>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
</div>

</body>
</html>