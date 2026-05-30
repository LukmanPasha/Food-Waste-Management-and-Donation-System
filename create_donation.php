<?php
// 1. Start the session at the absolute top of the file
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Security Check: Redirect to login if user session does not exist
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 3. Include your centralized database module (Uses PDO)
require_once 'db.php';

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_item    = isset($_POST['food_item']) ? trim($_POST['food_item']) : '';
    $contact_phone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
    
    // FIXED: Fallback check ensures donor_name is NEVER null, eliminating the Integrity Constraint violation
    $donor_name   = isset($_SESSION['email']) && !empty($_SESSION['email']) ? $_SESSION['email'] : 'Anonymous Donor'; 
    
    // Handle Image Upload
    $food_image = "";
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] == 0) {
        $target_dir = "uploads/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . "_" . basename($_FILES["food_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $target_file)) {
                $food_image = $file_name;
            } else {
                $error_msg = "Failed to upload the image file.";
            }
        } else {
            $error_msg = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error_msg)) {
        if (!empty($food_item) && !empty($contact_phone)) {
            try {
                $default_qty = "1 Batch";
                $default_status = "Pending";
                
                // Prepared statement to securely insert the data
                $query = "INSERT INTO donations (donor_name, food_item, quantity, phone, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                
                if ($stmt->execute([$donor_name, $food_item, $default_qty, $contact_phone, $default_status])) {
                    $success_msg = "Donation created successfully!";
                } else {
                    $error_msg = "Could not record the donation entries.";
                }
            } catch (PDOException $e) {
                $error_msg = "Database Error: " . $e->getMessage();
            }
        } else {
            $error_msg = "Please fill in all the required text fields.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Donation - Food Donate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Dimmed translucent linear shield layer over high-res food background image */
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=1600&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* Frosted Glassmorphism Form Container Card */
        .form-container {
            background: rgba(255, 255, 255, 0.11);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 50px 35px;
            width: 100%;
            max-width: 460px;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 35px;
            font-weight: 700;
            /* Vibrant colorful neon emerald text font gradient design */
            background: linear-gradient(135deg, #00b894, #55efc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.85rem;
            color: #00b894;
            font-weight: 600;
            margin-bottom: 8px;
            padding-left: 2px;
        }

        /* Translucent field border styling options */
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Customizing file upload interface properties cleanly */
        input[type="file"] {
            padding: 8px 12px;
            color: #e0e0e0;
            cursor: pointer;
        }

        input[type="file"]::file-selector-button {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            margin-right: 10px;
            transition: background 0.2s;
            font-weight: 500;
        }

        input[type="file"]::file-selector-button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        input[type="text"]:focus {
            border-color: #55efc4;
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 12px rgba(85, 239, 196, 0.25);
        }

        /* Solid Color Action Accent Button with Hover Glow */
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2ed573, #1abc9c);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 15px;
            box-shadow: 0 5px 15px rgba(46, 213, 115, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 213, 115, 0.55);
            background: linear-gradient(135deg, #26c267, #16a085);
        }

        button:active {
            transform: translateY(0);
        }

        /* Aesthetic custom alert panels */
        .error {
            color: #ff5252;
            background: rgba(255, 82, 82, 0.15);
            border-left: 4px solid #ff5252;
            padding: 12px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .success {
            color: #2ed573;
            background: rgba(46, 213, 115, 0.15);
            border-left: 4px solid #2ed573;
            padding: 12px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.4);
            padding-bottom: 2px;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #55efc4;
            border-bottom-color: #55efc4;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Create a Donation</h2>
        
        <?php if (!empty($error_msg)): ?>
            <div class="error">⚠️ <?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="success">🎉 <?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <form action="create_donation.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="food_item">Food Item Name:</label>
                <input type="text" id="food_item" name="food_item" placeholder="e.g., Daal Chawal" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="contact_phone">Contact Phone Number:</label>
                <input type="text" id="contact_phone" name="contact_phone" placeholder="e.g., 9876543210" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="food_image">Upload Food Image:</label>
                <input type="file" id="food_image" name="food_image" accept="image/*">
            </div>
            
            <button type="submit">Submit Donation</button>
        </form>
        
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

</body>
</html>