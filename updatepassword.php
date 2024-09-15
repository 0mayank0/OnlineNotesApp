<?php
// Start session and connect
session_start();
include('connection.php');

// Define error messages
$missingCurrentPassword = '<p><strong>Please enter your Current Password!</strong></p>';
$incorrectCurrentPassword = '<p><strong>The password entered is incorrect!</strong></p>';
$missingPassword = '<p><strong>Please enter a new Password!</strong></p>';
$invalidPassword = '<p><strong>Your password should be at least 6 characters long and include one capital letter and one number!</strong></p>';
$differentPassword = '<p><strong>Passwords don\'t match!</strong></p>';
$missingPassword2 = '<p><strong>Please confirm your password</strong></p>';

$errors = '';

// Check current password
if (empty($_POST["currentpassword"])) {
    $errors .= $missingCurrentPassword;
} else {
    $currentPassword = filter_var($_POST["currentpassword"], FILTER_SANITIZE_STRING);
    $currentPassword = hash('sha256', $currentPassword);
    $user_id = $_SESSION["user_id"];
    
    // Prepare and execute query to check current password
    $stmt = $link->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    $stmt->fetch();
    $stmt->close();
    
    if ($dbPassword !== $currentPassword) {
        $errors .= $incorrectCurrentPassword;
    }
}

// Validate new password
if (empty($_POST["password"])) {
    $errors .= $missingPassword;
} elseif (!(strlen($_POST["password"]) > 6 &&
           preg_match('/[A-Z]/', $_POST["password"]) &&
           preg_match('/[0-9]/', $_POST["password"]))) {
    $errors .= $invalidPassword;
} else {
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    if (empty($_POST["password2"])) {
        $errors .= $missingPassword2;
    } else {
        $password2 = filter_var($_POST["password2"], FILTER_SANITIZE_STRING);
        if ($password !== $password2) {
            $errors .= $differentPassword;
        }
    }
}

// If there are errors, print error message
if ($errors) {
    echo "<div class='alert alert-danger'>$errors</div>";
} else {
    // Hash new password
    $password = hash('sha256', $password);

    // Prepare and execute query to update password
    $stmt = $link->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $password, $user_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Your password has been updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>The password could not be reset. Please try again later.</div>";
    }
    
    $stmt->close();
}

// Close the database connection
$link->close();
?>
