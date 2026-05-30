<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your database connection file
include('connection.php');

// 2. Fetch the impact history from your 'food_donations' table
// Sorting by Fid DESC so your most recent contributions show up at the top
$sql = "SELECT * FROM food_donations ORDER BY Fid DESC";
$result = mysqli_query($connection, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Impact History</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header-section { margin-bottom: 25px; border-bottom: 2px solid #f1f3f5; padding-bottom: 15px; }
        .history-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .history-table th, .history-table td { padding: 12px; border: 1px solid #dee2e6; text-align: left; font-size: 0.95em; }
        .history-table th { background-color: #f1f3f5; font-weight: bold; color: #495057; }
        .badge-qty { background-color: #e2f0d9; color: #385723; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85em; }
        .badge-type { background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; }
        .btn-back { display: inline-block; margin-top: 20px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-back:hover { text-decoration: underline; }
        .empty-state { text-align: center; padding: 40px; color: #6c757d; border: 1px dashed #dee2e6; border-radius: 6px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>My Impact History</h1>
        <p style="color: #6c757d; margin: 0;">Welcome! Here you will be able to track your previous successful meal contributions.</p>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Food Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Type</th>
                    <th>Date / Time</th>
                    <th>Address / Location</th>
                    <th>Contact Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['food']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><span class="badge-qty"><?php echo htmlspecialchars($row['quantity']); ?></span></td>
                        <td><span class="badge-type"><?php echo htmlspecialchars($row['type']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']) . ' (' . htmlspecialchars($row['location']) . ')'; ?></td>
                        <td><?php echo htmlspecialchars($row['phoneno']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <h3>No past history found.</h3>
            <p>Your previous completed food distributions will start showing up here once added.</p>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
</div>

</body>
</html>