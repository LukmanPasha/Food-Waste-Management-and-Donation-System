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

// Safe fallback for city filter key using null coalescing operator
$city = $_GET['city'] ?? $_POST['city'] ?? '';

// Safe fallback for Delivery Person Session ID
$did = $_SESSION['Did'] ?? $_SESSION['did'] ?? 0;
$username = $_SESSION['username'] ?? 'Delivery Agent';

// Fetch public pending orders OR orders already assigned to this specific driver
$query = "SELECT * FROM food_donations WHERE status = 'pending' OR (delivery_person_id = '" . mysqli_real_escape_string($connection, $did) . "' AND status = 'accepted')";

// Apply city filter if a search term is provided
if (!empty($city)) {
    $escaped_city = mysqli_real_escape_string($connection, $city);
    $query = "SELECT * FROM food_donations WHERE (status = 'pending' OR (delivery_person_id = '" . mysqli_real_escape_string($connection, $did) . "' AND status = 'accepted')) 
              AND (location LIKE '%" . $escaped_city . "%' OR address LIKE '%" . $escaped_city . "%')";
}

$query .= " ORDER BY fid DESC";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard - Food Waste Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body { 
            /* Premium crisp food texture backdrop layered beneath an aesthetic translucent shield tint */
            background: linear-gradient(rgba(238, 242, 240, 0.8), rgba(220, 235, 228, 0.85)), 
                        url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #2c3e50;
        }

        /* Sleek Semi-Transparent Navigation Layer */
        .custom-navbar { 
            background: rgba(30, 41, 59, 0.85) !important; 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .custom-navbar .nav-link { 
            color: rgba(241, 245, 249, 0.9) !important; 
            font-weight: 500; 
            transition: color 0.2s ease;
        }
        
        .custom-navbar .nav-link:hover, .custom-navbar .nav-link.active { 
            color: #00b894 !important; 
        }
        
        /* Vibrant Emerald Headline Text with subtle text transparency shadow effects */
        .gradient-heading {
            font-weight: 700;
            background: linear-gradient(135deg, #00b894, #00cec9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 4px 10px rgba(0, 184, 148, 0.15);
        }

        /* Solid Color Action Accent Buttons with Hover Glow */
        .btn-green { 
            background: linear-gradient(135deg, #2ed573 0%, #1abc9c 100%); 
            color: white; 
            border: none; 
            font-weight: 600; 
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(46, 213, 115, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-green:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(46, 213, 115, 0.5);
            color: white;
        }

        /* Solid Dark Filter Button */
        .btn-dark-custom {
            background-color: #1e293b;
            color: white;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-dark-custom:hover {
            background-color: #0f172a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Premium Glassmorphism Panel Wrapper Layout with custom Transparency */
        .aesthetic-card {
            background: rgba(255, 255, 255, 0.45); /* Enhanced transparency lets the background peek through */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        /* High-transparency input fields styling */
        .form-control {
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.5);
            color: #1e293b;
            backdrop-filter: blur(5px);
        }
        
        .form-control:focus {
            border-color: #00b894;
            box-shadow: 0 0 0 0.25rem rgba(0, 184, 148, 0.15);
            background: rgba(255, 255, 255, 0.9);
        }

        /* Table Transparency Aesthetics */
        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
            background: transparent;
        }
        
        .table thead th {
            border: none;
            color: rgba(30, 41, 59, 0.7); /* Soft transparent dark coloring */
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding-bottom: 12px;
        }
        
        .table tbody tr {
            background-color: rgba(255, 255, 255, 0.65); /* Translucent rows */
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.01);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            transform: translateY(-2px);
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        .table tbody td {
            padding: 18px 16px;
            border: none;
        }
        
        .table tbody tr td:first-child { border-radius: 12px 0 0 12px; }
        .table tbody tr td:last-child { border-radius: 0 12px 12px 0; }

        /* Status Label Badges with translucent solid coloring layers */
        .badge-accepted { 
            background: rgba(46, 213, 115, 0.2); 
            color: #16a085; 
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .badge-pending { 
            background: rgba(255, 159, 67, 0.2); 
            color: #d35400; 
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        /* Floating Graphic Illustration Hover Animation rules */
        .floating-img {
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.08));
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg custom-navbar sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-white fw-bold fs-4" href="delivery.php">Food <span style="color: #00b894;">Donate</span></a>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto text-center">
                    <li class="nav-item"><a class="nav-link active" href="delivery.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="map.php">Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="deliverymyord.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger fw-semibold" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5 text-center">
        <h2 class="fw-bold mb-2 text-dark">Welcome, <span class="gradient-heading"><?php echo htmlspecialchars($username); ?></span>!</h2>
        <p class="text-secondary mb-4 fw-medium" style="color: rgba(44, 62, 80, 0.8) !important;">Manage cluster requests and delivery tracks effortlessly.</p>
        
        <div class="row justify-content-center mb-4">
            <div class="col-md-6 col-lg-5">
                <form action="delivery.php" method="GET" class="d-flex gap-2">
                    <input type="text" name="city" class="form-control shadow-sm" placeholder="Search by city/address..." value="<?php echo htmlspecialchars($city); ?>">
                    <button type="submit" class="btn btn-dark-custom px-4 shadow-sm">Filter</button>
                    <?php if(!empty($city)): ?>
                        <a href="delivery.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3" style="border-radius: 10px; background: rgba(255,255,255,0.4);">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="mb-5">
            <img class="floating-img" src="https://img.freepik.com/free-vector/delivery-staff-riding-scooter-motorcycle-smartphone-with-map-gps-tracking-delivery-around-world-concept_1150-34879.jpg" alt="Food Delivery Track Illustration" style="max-height: 200px; width: auto; object-fit: contain;">
        </div>

        <div class="card aesthetic-card p-4 text-start border-0">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <h5 class="fw-bold mb-0 text-dark fs-4">Delivery Requests & My Orders</h5>
                <a href="deliverymyord.php" class="btn btn-green px-4 py-2 shadow-sm">My Orders Log</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone No</th>
                            <th>Date / Time</th>
                            <th>Pickup Point</th>
                            <th>Delivery Point</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $current_status = $row['status'] ?? 'pending';
                                echo "<tr>";
                                echo "<td class='fw-bold text-dark'>" . htmlspecialchars($row['name'] ?? 'Donor') . "</td>";
                                echo "<td class='text-secondary' style='color: rgba(100, 116, 139, 0.95) !important;'>" . htmlspecialchars($row['phoneno'] ?? $row['phone'] ?? 'N/A') . "</td>";
                                echo "<td class='text-secondary' style='color: rgba(100, 116, 139, 0.95) !important;'>" . htmlspecialchars($row['date'] ?? $row['datetime'] ?? 'Today') . "</td>";
                                echo "<td class='text-secondary' style='color: rgba(100, 116, 139, 0.95) !important;'>" . htmlspecialchars($row['location'] ?? $row['address'] ?? 'Pickup Point') . "</td>";
                                echo "<td class='text-secondary' style='color: rgba(100, 116, 139, 0.95) !important;'>" . htmlspecialchars($row['delivery_address'] ?? 'Assigned Shelter') . "</td>";
                                
                                // Conditional Styling Status Badge logic columns
                                if ($current_status == 'accepted') {
                                    echo "<td><span class='badge-accepted'>My Order</span></td>";
                                    echo "<td><button class='btn btn-sm btn-light border px-3 text-muted' style='border-radius: 20px; font-size: 0.85rem; background: rgba(255,255,255,0.4);' disabled>Accepted</button></td>";
                                } else {
                                    echo "<td><span class='badge-pending'>Available</span></td>";
                                    echo "<td><a href='accept_order.php?id=" . urlencode($row['fid'] ?? $row['id'] ?? '') . "' class='btn btn-sm btn-green px-4 py-1' style='font-size: 0.85rem;'>Accept</a></td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted fw-medium' style='border-radius: 12px; background: rgba(255,255,255,0.35); backdrop-filter: blur(5px);'>No active or accepted donations found in your view profile.</td></tr>";
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
mysqli_close($connection);
?>