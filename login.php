<?php
// Start session
session_start();

// Connect to the database
include("connection.php");

// Define error messages
$missingEmail = '<p><strong>Please enter your email address!</strong></p>';
$missingPassword = '<p><strong>Please enter your password!</strong></p>';

// Initialize errors variable
$errors = '';

// Get and sanitize user inputs
$email = isset($_POST["loginemail"]) ? filter_var($_POST["loginemail"], FILTER_SANITIZE_EMAIL) : '';
$password = isset($_POST["loginpassword"]) ? filter_var($_POST["loginpassword"], FILTER_SANITIZE_STRING) : '';

// Check for missing inputs
if (empty($email)) {
    $errors .= $missingEmail;
}
if (empty($password)) {
    $errors .= $missingPassword;
}

// If there are any errors, print error message and exit
if ($errors) {
    echo '<div class="alert alert-danger">' . $errors . '</div>';
    exit;
}

// Prepare variables for the query
$email = mysqli_real_escape_string($link, $email);
$password = mysqli_real_escape_string($link, hash('sha256', $password));

// Run query to check combination of email & password
$sql = "SELECT * FROM users WHERE email='$email' AND password='$password' AND activation='activated'";
$result = mysqli_query($link, $sql);

if (!$result) {
    echo '<div class="alert alert-danger">Error running the query!</div>';
    exit;
}

// If email & password don't match, print error
$count = mysqli_num_rows($result);
if ($count !== 1) {
    echo '<div class="alert alert-danger">Wrong Username or Password</div>';
} else {
    // Log the user in: Set session variables
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email'] = $row['email'];
    
    if (empty($_POST['rememberme'])) {
        // If remember me is not checked
        echo "success";
    } else {
        // Create two variables $authentificator1 and $authentificator2
        $authentificator1 = bin2hex(openssl_random_pseudo_bytes(10));
        $authentificator2 = openssl_random_pseudo_bytes(20);

        // Store them in a cookie
        $cookieValue = $authentificator1 . "," . bin2hex($authentificator2);
        setcookie("rememberme", $cookieValue, time() + 1296000, "/", "", false, true);

        // Store hashed values in rememberme table
        $f2authentificator2 = hash('sha256', $authentificator2);
        $user_id = $_SESSION['user_id'];
        $expiration = date('Y-m-d H:i:s', time() + 1296000);

        $stmt = $link->prepare("INSERT INTO rememberme (authentificator1, f2authentificator2, user_id, expires) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $authentificator1, $f2authentificator2, $user_id, $expiration);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo '<div class="alert alert-danger">There was an error storing data to remember you next time.</div>';
        }

        $stmt->close();
    }
}

// Close the database connection
$link->close();
?>
