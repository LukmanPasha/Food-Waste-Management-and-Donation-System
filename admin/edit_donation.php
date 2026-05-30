<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your database connection file from the parent folder
include('../connection.php');

// 2. SELF-HEALING FEATURE: If no ID is specified, create a mock row automatically for testing
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $test_name = "Test Donor";
    $test_food = "Sample Rice Item";
    $test_category = "Dry Rations";
    $test_qty = "5 KG";
    $test_date = date('Y-m-d H:i:s');
    $test_phone = "9876543210";
    $test_addr = "123 Main Street";
    
    $insert_mock = "INSERT INTO food_donations (name, email, food, type, category, quantity, date, address, location, phoneno) 
                    VALUES ('$test_name', 'test@gmail.com', '$test_food', 'Veg', '$test_category', '$test_qty', '$test_date', '$test_addr', 'Local', '$test_phone')";
    
    if (mysqli_query($connection, $insert_mock)) {
        $new_id = mysqli_insert_id($connection);
        // Refresh the page with the newly created ID
        header("Location: edit_donation.php?id=" . $new_id);
        exit();
    } else {
        die("Database Initialization Error: " . mysqli_error($connection));
    }
}

$fid = intval($_GET['id']);

// 3. Handle Form Submission (When admin saves changes)
if (isset($_POST['update_donation'])) {
    $food = mysqli_real_escape_string($connection, $_POST['food']);
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    $quantity = mysqli_real_escape_string($connection, $_POST['quantity']);
    $type = mysqli_real_escape_string($connection, $_POST['type']);
    $phoneno = mysqli_real_escape_string($connection, $_POST['phoneno']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    
    $update_sql = "UPDATE food_donations SET food = ?, category = ?, quantity = ?, type = ?, phoneno = ?, address = ? WHERE Fid = ?";
    
    if ($stmt = mysqli_prepare($connection, $update_sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $food, $category, $quantity, $type, $phoneno, $address, $fid);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: manage_donations.php");
            exit();
        } else {
            echo "Database Error: Could not save adjustments. " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}

// 4. Load the data to populate input fields
$query = mysqli_query($connection, "SELECT * FROM food_donations WHERE Fid = $fid");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    die("Error: Specified donation record #$fid was not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Donation Record</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; }
        .edit-container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        label { font-weight: bold; display: block; margin-top: 15px; margin-bottom: 5px; color: #495057; }
        input[type="text"], select { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; font-size: 1em; }
        .btn-submit { background-color: #0d6efd; color: white; padding: 12px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 25px; font-size: 1em; }
        .btn-submit:hover { background-color: #0b5ed7; }
        .cancel-link { display: block; text-align: center; margin-top: 15px; color: #6c757d; text-decoration: none; }
        .cancel-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>✏️ Edit Donation Record #<?php echo $row['Fid']; ?></h2>

    <form action="" method="POST">
        <label for="food">Food Item Name:</label>
        <input type="text" id="food" name="food" value="<?php echo htmlspecialchars($row['food']); ?>" required>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="text" id="quantity" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required>

        <label for="type">Type:</label>
        <select id="type" name="type">
            <option value="Veg" <?php if($row['type'] == 'Veg') echo 'selected'; ?>>Veg</option>
            <option value="Non-Veg" <?php if($row['type'] == 'Non-Veg') echo 'selected'; ?>>Non-Veg</option>
        </select>

        <label for="phoneno">Contact Phone Number:</label>
        <input type="text" id="phoneno" name="phoneno" value="<?php echo htmlspecialchars($row['phoneno']); ?>" required>

        <label for="address">Pickup Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>

        <button type="submit" name="update_donation" class="btn-submit">Save Changes</button>
    </form>

    <a href="manage_donations.php" class="cancel-link">Cancel and Go Back</a>
</div>

</body>
</html>