<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
?>
<h1>My Impact History</h1>
<p>Your history table coming soon... <a href="homepg.php">Back to Dashboard</a></p>