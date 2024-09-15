<?php
session_start();
include('connection.php'); // Ensure the connection is included

if (!isset($_SESSION['user_id']) && !empty($_COOKIE['rememberme'])) {
    // Extract and process the cookie
    list($authentificator1, $authentificator2) = explode(',', $_COOKIE['rememberme']);
    $authentificator2 = hex2bin($authentificator2);
    $f2authentificator2 = hash('sha256', $authentificator2);

    // Prepare and execute query
    $stmt = $link->prepare("SELECT * FROM rememberme WHERE authentificator1 = ?");
    $stmt->bind_param("s", $authentificator1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo '<div class="alert alert-danger">Remember me process failed!</div>';
        exit;
    }

    $row = $result->fetch_assoc();
    if (!hash_equals($row['f2authentificator2'], $f2authentificator2)) {
        echo '<div class="alert alert-danger">Invalid authentication.</div>';
        exit;
    }

    // Generate new tokens
    $authentificator1 = bin2hex(openssl_random_pseudo_bytes(10));
    $authentificator2 = openssl_random_pseudo_bytes(20);
    $cookieValue = $authentificator1 . "," . bin2hex($authentificator2);
    $f2authentificator2 = hash('sha256', $authentificator2);
    $expiration = date('Y-m-d H:i:s', time() + 1296000);

    // Store new tokens in the cookie
    setcookie("rememberme", $cookieValue, time() + 1296000, "/", "", true, true);

    // Prepare and execute query to update tokens
    $stmt = $link->prepare("INSERT INTO rememberme (authentificator1, f2authentificator2, user_id, expires) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $authentificator1, $f2authentificator2, $row['user_id'], $expiration);
    if (!$stmt->execute()) {
        echo '<div class="alert alert-danger">There was an error storing data to remember you next time.</div>';
        exit;
    }

    // Log the user in and redirect
    $_SESSION['user_id'] = $row['user_id'];
    header("Location: mainpageloggedin.php");
    exit;
}
?>
