<?php
// Start the session to manage delivery person login states
session_start();

// Database Connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "food_waste";

// Using $connection as per your database configuration
$connection = mysqli_connect($servername, $db_username, $db_password, $dbname);

// Check database connection safely
if (!$connection) {
    die("<div class='alert alert-danger m-3'>Database connection failed: " . mysqli_connect_error() . "</div>");
}

// Safe fallback for Delivery Person Session ID
$did = $_SESSION['Did'] ?? $_SESSION['did'] ?? 0;
$username = $_SESSION['username'] ?? 'Delivery Agent';

// Escape the session ID using the correct $connection variable
$escaped_did = mysqli_real_escape_string($connection, $did);

/* NOTE ON COLUMN NAMES:
  If your table uses a column like 'did' or 'assigned_to' instead of 'delivery_person_id',
  change 'delivery_person_id' below to match your exact database column structure.
*/
$query = "SELECT * FROM food_donations WHERE delivery_person_id = '$escaped_did' AND status = 'accepted' ORDER BY fid DESC";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Food Donate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .navbar-brand span { color: #2ecc71; }
        .custom-navbar { background-color: #2c3e50; }
        .custom-navbar .nav-link { color: #ecf0f1 !important; }
        .custom-navbar .nav-link:hover, .custom-navbar .nav-link.active { color: #2ecc71 !important; }
        .btn-green { background-color: #2ecc71; color: white; border: none; font-weight: bold; }
        .btn-green:hover { background-color: #27ae60; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg custom-navbar sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="delivery.php">Food <span style="color: #2ecc71;">Donate</span></a>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="delivery.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="map.php">Map</a></li>
                    <li class="nav-item"><a class="nav-link active" href="deliverymyord.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card shadow-sm border-0 bg-white p-4 mx-auto" style="max-width: 900px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="fw-bold mb-1">Orders Assigned to You</h3>
                    <p class="text-muted small mb-0">Please pick up and deliver these packages promptly.</p>
                </div>
                <a href="delivery.php" class="btn btn-sm btn-green px-3">Take Orders</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Food Description</th>
                            <th>Quantity</th>
                            <th>Pickup Address</th>
                            <th>Status/Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='fw-bold'>#" . htmlspecialchars($row['fid'] ?? $row['id'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($row['food'] ?? $row['description'] ?? 'Meal Package') . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity'] ?? '1') . "</td>";
                                echo "<td>" . htmlspecialchars($row['location'] ?? $row['address'] ?? 'Pickup Point') . "</td>";
                                echo "<td><span class='badge bg-success p-2'>On The Way</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted small'>You have no active food delivery shipments assigned to your profile right now.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Correctly closing the connection using your variable
mysqli_close($connection);
?>