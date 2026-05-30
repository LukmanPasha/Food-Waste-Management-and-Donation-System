<?php
// 1. Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_waste";

$connection = mysqli_connect($servername, $username, $password, $dbname);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// 2. Fetch records from the 'donations' table
$query = "SELECT id, food_item_name, contact_phone, food_image FROM donations ORDER BY id DESC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Donations - Food Donate</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; display: flex; justify-content: center; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 900px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 28px; color: #333; }
        .btn-new { background-color: #1b8a5a; color: white; text-decoration: none; padding: 10px 18px; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-new:hover { background-color: #146642; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #555; font-weight: bold; }
        .food-img { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .no-records { text-align: center; padding: 40px; color: #777; font-size: 16px; border: 2px dashed #ddd; border-radius: 6px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <div>
            <h1>My Donations</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Track the food supply listings you have submitted.</p>
        </div>
        <a href="create_donation.php" class="btn-new">+ New Donation</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Food Item Name</th>
                    <th>Contact Phone</th>
                    <th>Image Attachment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['food_item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_phone']); ?></td>
                        <td>
                            <?php 
                            $image_path = "uploads/" . $row['food_image'];
                            if (!empty($row['food_image']) && file_exists($image_path)): 
                            ?>
                                <a href="<?php echo $image_path; ?>" target="_blank">
                                    <img src="<?php echo $image_path; ?>" class="food-img" alt="Food">
                                </a>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">No image</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-records">
            <h3>You haven't made any donation listings yet.</h3>
            <p>Your active listings and database entries will appear right here.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>