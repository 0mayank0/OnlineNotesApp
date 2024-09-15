<?php
// Start session and connect
session_start();
include('connection.php');

// Get user_id and new email sent through Ajax
$user_id = $_SESSION['user_id'];
$newemail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Validate new email
if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
    echo "<div class='alert alert-danger'>Please enter a valid email address.</div>";
    exit;
}

// Check if new email exists
$stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $newemail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<div class='alert alert-danger'>There is already a user registered with that email! Please choose another one!</div>";
    exit;
}

// Get the current email
$stmt = $link->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
} else {
    echo "<div class='alert alert-danger'>There was an error retrieving the email from the database.</div>";
    exit;
}

// Create a unique activation code
$activationKey = bin2hex(openssl_random_pseudo_bytes(16));

// Insert new activation code in the users table
$stmt = $link->prepare("UPDATE users SET activation2 = ? WHERE user_id = ?");
$stmt->bind_param("si", $activationKey, $user_id);
if (!$stmt->execute()) {
    echo "<div class='alert alert-danger'>There was an error updating the user details in the database.</div>";
    exit;
} else {
    // Send email with link to activatenewemail.php
    $message = "Please click on this link to confirm you own this email address:\n\n";
    $message .= "http://mynotes.thecompletewebhosting.com/activatenewemail.php?email=" . urlencode($email) . "&newemail=" . urlencode($newemail) . "&key=$activationKey";
    if (mail($newemail, 'Email Update for your Online Notes App', $message, 'From: developmentisland@gmail.com')) {
        echo "<div class='alert alert-success'>An email has been sent to $newemail. Please click on the link to confirm the update.</div>";
    } else {
        echo "<div class='alert alert-danger'>There was an error sending the confirmation email.</div>";
    }
}
?>
