<?php
// Start session and connect
session_start();
include('connection.php');

// Get user_id
$id = $_SESSION['user_id'];

// Get username sent through Ajax
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);

// Validate username (optional, you can customize this)
if (empty($username) || strlen($username) < 3) {
    echo '<div class="alert alert-danger">Please provide a valid username with at least 3 characters.</div>';
    exit;
}

// Prepare and execute query to update username
$stmt = $link->prepare("UPDATE users SET username = ? WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("si", $username, $id);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Username updated successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">There was an error updating the username. Please try again later.</div>';
    }
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Database error: Unable to prepare the statement.</div>';
}

// Close the database connection
$link->close();
?>
