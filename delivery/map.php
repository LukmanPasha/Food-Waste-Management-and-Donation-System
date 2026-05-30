<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? 'Delivery Agent';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Map - Food Donate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body { 
            background: linear-gradient(rgba(238, 242, 240, 0.8), rgba(220, 235, 228, 0.85)), 
                        url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #2c3e50;
        }

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
        
        .custom-navbar .nav-link:hover { color: #00b894 !important; }

        .gradient-heading {
            font-weight: 700;
            background: linear-gradient(135deg, #00b894, #00cec9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-back-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-back-dashboard:hover {
            background: linear-gradient(135deg, #00b894, #1abc9c);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .map-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .form-control-custom {
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
        }

        .btn-search-custom {
            background-color: #00b894;
            color: white;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            padding: 0 25px;
            transition: all 0.3s ease;
        }

        .btn-search-custom:hover {
            background-color: #00a383;
            transform: translateY(-1px);
        }

        #live-map {
            width: 100%;
            height: 550px;
            border-radius: 16px;
            z-index: 1;
        }

        /* Customize instructions card overlay layout slightly to avoid breaking UI flow */
        .leaflet-routing-container {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(5px);
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
            max-height: 300px;
            overflow-y: auto;
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
                    <li class="nav-item"><a class="nav-link" href="delivery.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="map.php">Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="deliverymyord.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger fw-semibold" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-dark">Delivery Route <span class="gradient-heading">Navigation</span></h2>
                <p class="text-secondary mb-0 fw-medium">Calculate route metrics instantly via map search.</p>
            </div>
            
            <a href="delivery.php" class="btn-back-dashboard">
                <i class="fa-solid fa-arrow-left"></i> Back to Delivery Dashboard
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex gap-2">
                    <input type="text" id="destination-input" class="form-control form-control-custom shadow-sm" placeholder="Type destination city name (e.g. Mysuru, Tumakuru)...">
                    <button id="search-route-btn" class="btn btn-search-custom shadow-sm">Search Route</button>
                </div>
            </div>
        </div>

        <div class="card map-card p-4 border-0">
            <div id="live-map"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Initialize operations hub default baseline coordinate system (Bengaluru)
            var hubLatLng = [12.9716, 77.5946];
            var map = L.map('live-map').setView(hubLatLng, 9);

            // 2. Load open tile layout configurations
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Drop initial center hub marker pinning our starting node
            var hubMarker = L.marker(hubLatLng).addTo(map)
                .bindPopup("<b>Operations Center Hub</b><br>Starting point for delivery routes.").openPopup();

            // 3. Keep a global variable handle to trace or clear routing lines cleanly
            var routingControl = null;

            // 4. Live Geocoding + Route Drawing Callback Node Logic
            function calculateRoute() {
                var cityInput = document.getElementById('destination-input').value.trim();
                if (!cityInput) {
                    alert("Please enter a destination city name first.");
                    return;
                }

                // Append regional context state identifier helper elements safely to isolate responses natively
                var geoUrl = "https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(cityInput + ", Karnataka, India");

                fetch(geoUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            var destLat = parseFloat(data[0].lat);
                            var destLng = parseFloat(data[0].lon);
                            var destLatLng = [destLat, destLng];

                            // Clean clear old line layouts if another route search executes sequentially
                            if (routingControl !== null) {
                                map.removeControl(routingControl);
                            }

                            // Remove initial popup to clean space workspace
                            map.closePopup();

                            // Instantiate the interactive routing path calculation module block
                            routingControl = L.Routing.control({
                                waypoints: [
                                    L.latLng(hubLatLng[0], hubLatLng[1]), // Origin: Bengaluru Hub
                                    L.latLng(destLatLng[0], destLatLng[1]) // Destination: Searched city
                                ],
                                routeWhileDragging: false,
                                addWaypoints: false,
                                draggableWaypoints: false,
                                lineOptions: {
                                    styles: [{ color: '#00b894', opacity: 0.85, weight: 6 }] // App themed green path line
                                },
                                show: true // Displays turn-by-turn instruction panel card automatically
                            }).addTo(map);

                        } else {
                            alert("Location not found. Please try refining your city search term.");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("An error occurred while matching coordinates. Please check your network connection.");
                    });
            }

            // Bind triggers to Search button click event & Enter key press state nodes safely
            document.getElementById('search-route-btn').addEventListener('click', calculateRoute);
            document.getElementById('destination-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    calculateRoute();
                }
            });
        });
    </script>
</body>
</html>