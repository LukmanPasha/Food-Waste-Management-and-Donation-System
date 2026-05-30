<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your database connection file from the parent directory
include('../connection.php');

// 2. Fetch feedback entries from your exact 'user_feedback' table
$sql = "SELECT * FROM user_feedback ORDER BY id DESC";
$result = mysqli_query($connection, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Feedback</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .feedback-container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { color: #333; margin-top: 0; border-bottom: 2px solid #9b51e0; padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .feedback-table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        .feedback-table th, .feedback-table td { padding: 12px 15px; border: 1px solid #dee2e6; font-size: 0.95em; }
        .feedback-table th { background-color: #9b51e0; color: white; font-weight: 600; }
        .feedback-table tr:nth-child(even) { background-color: #f8f9fa; }
        .empty-row { text-align: center; padding: 30px; color: #6c757d; font-style: italic; }
        .btn-back { display: inline-block; margin-top: 20px; color: #9b51e0; text-decoration: none; font-weight: bold; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="feedback-container">
    <h2>💬 User Feedback & Messages</h2>

    <table class="feedback-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty-row">No feedback messages found in the database records.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <a href="index.php" class="btn-back">← Back to Admin Home</a>
</div>

</body>
</html>