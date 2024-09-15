<?php
session_start(); // Ensure session is started

if (isset($_SESSION['user_id']) && isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Destroy session
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session

    // Clear remember me cookie
    setcookie("rememberme", "", time() - 3600, "/", "", false, true); // Use HttpOnly flag

    // Redirect to login page or homepage
    header("Location: login.php"); // Redirect to a login page or home page
    exit;
}
?>
