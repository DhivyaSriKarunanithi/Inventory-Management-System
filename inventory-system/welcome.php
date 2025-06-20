<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

echo "<h2>Welcome to the Dashboard, " . $_SESSION['username'] . "!</h2>";
echo "<p>You have logged in successfully.</p>";
echo '<a href="logout.php">Logout</a>';
?>
