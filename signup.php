<?php
// Start session
session_start();
include('connection.php'); 

// Define error messages
$errors = '';
$missingUsername = '<p><strong>Please enter a username!</strong></p>';
$missingEmail = '<p><strong>Please enter your email address!</strong></p>';
$invalidEmail = '<p><strong>Please enter a valid email address!</strong></p>';
$missingPassword = '<p><strong>Please enter a Password!</strong></p>';
$invalidPassword = '<p><strong>Your password should be at least 6 characters long and include one capital letter and one number!</strong></p>';
$differentPassword = '<p><strong>Passwords don\'t match!</strong></p>';
$missingPassword2 = '<p><strong>Please confirm your password</strong></p>';

// Get username
if (empty($_POST["username"])) {
    $errors .= $missingUsername;
} else {
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
}

// Get email
if (empty($_POST["email"])) {
    $errors .= $missingEmail;
} else {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors .= $invalidEmail;
    }
}

// Get passwords
if (empty($_POST["password"])) {
    $errors .= $missingPassword;
} elseif (!(strlen($_POST["password"]) >= 6
         && preg_match('/[A-Z]/', $_POST["password"])
         && preg_match('/[0-9]/', $_POST["password"])
        )) {
    $errors .= $invalidPassword;
} else {
    $password = $_POST["password"];
    if (empty($_POST["password2"])) {
        $errors .= $missingPassword2;
    } else {
        $password2 = $_POST["password2"];
        if ($password !== $password2) {
            $errors .= $differentPassword;
        }
    }
}

// If there are any errors, print error
if ($errors) {
    echo '<div class="alert alert-danger">' . $errors . '</div>';
    exit;
}

// No errors
$username = mysqli_real_escape_string($link, $username);
$email = mysqli_real_escape_string($link, $email);
$password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

// Check if username exists
$stmt = $link->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo '<div class="alert alert-danger">That username is already registered. Do you want to log in?</div>';
    exit;
}

// Check if email exists
$stmt = $link->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo '<div class="alert alert-danger">That email is already registered. Do you want to log in?</div>';
    exit;
}

// Create a unique activation code
$activationKey = bin2hex(openssl_random_pseudo_bytes(16));

// Insert user details and activation code in the users table
$stmt = $link->prepare("INSERT INTO users (username, email, password, activation) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password, $activationKey);
if (!$stmt->execute()) {
    echo '<div class="alert alert-danger">There was an error inserting the user details into the database!</div>';
    exit;
}

// Send the user an email with a link to activate.php
$message = "Please click on this link to activate your account:\n\n";
$projectRoot = "https://completewebdevelopmentcourse.com/WEBSITES/9.%20Notes%20App%20(Bootstrap%20PHP%20mySQL)/";
$message .= $projectRoot . "activate.php?email=" . urlencode($email) . "&key=$activationKey";

$headers = 'From: developmentisland@gmail.com';
if (mail($email, 'Confirm your Registration', $message, $headers)) {
    echo "<div class='alert alert-success'>Thank you for registering! A confirmation email has been sent to $email. Please click on the activation link to activate your account.</div>";
} else {
    echo '<div class="alert alert-danger">There was an error sending the confirmation email. Please try again later.</div>';
}
?>
