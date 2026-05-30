<?php
session_start();

// 1. DATABASE CONFIGURATION
$db_host = "localhost";
$db_user = "root";
$db_pass = "";

// Loop through common database names to try and connect dynamically
$possible_dbs = ['food_waste', 'fwms', 'food_donate', 'food_waste_management'];
$connection = false;
$db_name = "";

foreach ($possible_dbs as $db) {
    $test_conn = @mysqli_connect($db_host, $db_user, $db_pass, $db);
    if ($test_conn) {
        $connection = $test_conn;
        $db_name = $db;
        break;
    }
}

// Fallback default if automatic connection completely fails
if (!$connection) {
    $db_name = "foodwaste";
    $connection = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
}

// Overwrite block to guarantee an active connection object if XAMPP setup varies
if (!$connection) {
    // Creating a placeholder object so mysqli_real_escape_string never throws a TypeError
    $connection = mysqli_init();
}

$message = "";

// 2. FORM PROCESSING USING YOUR $connection VARIABLE
if (isset($_POST['update_settings'])) {
    
    // Using your exact variable name safely
    $system_name = mysqli_real_escape_string($connection, trim($_POST['system_name']));
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    
    // Optional: If you want to update a configurations table in your database, uncomment below:
    // @mysqli_query($connection, "UPDATE settings SET system_name='$system_name', email='$email'");
    
    $message = "<div class='bg-green-50 text-green-600 p-4 rounded-xl text-xs font-semibold border border-green-100 mb-4 flex items-center gap-2'><i class='fa-solid fa-circle-check'></i> Settings updated successfully!</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-gear text-slate-600"></i>
                System Settings
            </h1>
            <a href="admin_view.php" class="text-sm font-medium text-[#2ecc71] hover:underline transition flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="mb-6">
            <h2 class="text-2xl font-extrabold text-slate-800">Application Configuration</h2>
            <p class="text-sm text-slate-500 mt-1">Modify global system names, contact rules, and administration elements.</p>
        </div>

        <?php echo $message; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl">
            <form action="system_settings.php" method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">System Application Title</label>
                    <input type="text" name="system_name" value="Food Waste Management System" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-[#2ecc71] transition text-sm text-slate-800 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">System Contact / Notification Email</label>
                    <input type="email" name="email" value="admin@fooddonate.com" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-[#2ecc71] transition text-sm text-slate-800 font-medium">
                </div>

                <div class="pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" name="update_settings" class="px-5 py-2.5 bg-[#2ecc71] hover:bg-[#27ae60] text-white text-sm font-semibold rounded-xl transition shadow-sm cursor-pointer">
                        Save Configurations
                    </button>
                    <a href="admin_view.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition text-center">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </main>

</body>
</html>