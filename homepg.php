<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Food Donate</title>
    <style>
        /* Fullscreen background setup with your needy/poor persons background image */
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), 
                        url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed; 
            background-size: cover;
            min-height: 100vh;
            display: flex; 
            flex-direction: column;
            align-items: center; 
        }

        /* Top Navigation Bar containing all your missing links */
        .navbar {
            width: 100%;
            background-color: rgba(20, 40, 30, 0.95);
            padding: 15px 40px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }
        .navbar-brand {
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
        }
        .navbar-brand span {
            color: #2ecc71;
        }

        /* Navigation Menu Items */
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .nav-item {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-item:hover {
            color: #2ecc71;
        }
        
        /* Specialized Exit/Logout Button Styling */
        .btn-exit {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn-exit:hover {
            background-color: #c0392b;
            color: white;
        }

        /* Welcome Greeting Banner */
        .welcome-banner {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-left: 5px solid #2ecc71;
            padding: 20px;
            margin: 40px 20px 10px 20px;
            border-radius: 4px;
            max-width: 900px;
            width: calc(100% - 40px);
            box-sizing: border-box;
            color: #ffffff;
        }
        .welcome-banner h1 { margin: 0 0 5px 0; font-size: 28px; }
        .welcome-banner p { margin: 0; font-size: 16px; color: #e0e0e0; }

        /* Actions Grid Layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            max-width: 940px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Action Feature Cards */
        .card { 
            background: rgba(255, 255, 255, 0.96); 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.4); 
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h2 { margin-top: 0; font-size: 24px; color: #2c3e50; }
        .card p { color: #555; font-size: 15px; line-height: 1.5; margin-bottom: 20px; }
        
        /* Grid Buttons */
        .btn { 
            background-color: #2ecc71; 
            color: white; 
            text-decoration: none; 
            padding: 12px 20px; 
            border-radius: 4px; 
            font-size: 16px; 
            cursor: pointer; 
            font-weight: bold; 
            display: inline-block;
            transition: background 0.2s;
        }
        .btn:hover { background-color: #27ae60; }

        /* System Footer Container */
        .footer {
            margin-top: auto;
            width: 100%;
            text-align: center;
            padding: 20px;
            color: #ccc;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.7);
            box-sizing: border-box;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="homepg.php" class="navbar-brand">Food <span>Donate</span></a>
        
        <div class="nav-links">
            <a href="homepg.php" class="nav-item">Home</a>
            <a href="create_donation.php" class="nav-item">Donate Now</a>
            <a href="view_demands.php" class="nav-item">Active Demands</a>
            <a href="donate.php" class="nav-item">My Donations</a>
            <a href="logout.php" class="nav-item btn-exit">Exit</a>
        </div>
    </div>

    <div class="welcome-banner">
        <h1>Welcome back, Guest!</h1>
        <p>Thank you for stopping by. Choose an action below or use the top menu to navigate options.</p>
    </div>

    <div class="grid-container">
        
        <div class="card">
            <div>
                <h2>Donate Food</h2>
                <p>Have excess food from an event, restaurant, or home? Submit a donation request here to reach nearby shelters.</p>
            </div>
            <a href="create_donation.php" class="btn">Create Donation</a>
        </div>
        
        <div class="card">
            <div>
                <h2>Active Requests</h2>
                <p>Browse through lists of local charities, orphanages, and food distribution centers in need of immediate supplies.</p>
            </div>
            <a href="view_demands.php" class="btn">View Demands</a>
        </div>
        
        <div class="card">
            <div>
                <h2>My Impact History</h2>
                <p>Track all your previous successful meal contributions, generated receipts, and view community badges earned.</p>
            </div>
            <a href="donate.php" class="btn">Open Records</a>
        </div>

    </div>

    <div class="footer">
        &copy; 2026 Food Waste Management System. Making a difference, one meal at a time.
    </div>

</body>
</html>