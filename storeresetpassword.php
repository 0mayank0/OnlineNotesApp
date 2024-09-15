<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = mysqli_real_escape_string($link, $_POST['user_id']);
    $key = mysqli_real_escape_string($link, $_POST['key']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // Validate passwords
    if ($password !== $password2) {
        echo '<div class="alert alert-danger">Passwords do not match.</div>';
        exit;
    }

    if (strlen($password) < 6) {
        echo '<div class="alert alert-danger">Password must be at least 6 characters long.</div>';
        exit;
    }

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the user's password
    $stmt = $link->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    if (!$stmt->execute()) {
        echo '<div class="alert alert-danger">Error updating password. Please try again.</div>';
        exit;
    }

    // Update the forgotpassword table to mark the reset as completed
    $stmt = $link->prepare("UPDATE forgotpassword SET status = 'completed' WHERE rkey = ? AND user_id = ?");
    $stmt->bind_param("si", $key, $user_id);
    if (!$stmt->execute()) {
        echo '<div class="alert alert-danger">Error updating reset request. Please try again.</div>';
        exit;
    }

    echo '<div class="alert alert-success">Your password has been successfully reset. You can now <a href="login.php">log in</a>.</div>';
}
?>
