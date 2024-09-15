<?php
session_start();
include('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1 {
            color: purple;   
        }
        .contactForm {
            border: 1px solid #7c73f6;
            margin-top: 50px;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-offset-1 col-sm-10 contactForm">
                <h1>Reset Password:</h1>
                <div id="resultmessage"></div>
                <?php
                if (!isset($_GET['user_id']) || !isset($_GET['key'])) {
                    echo '<div class="alert alert-danger">There was an error. Please click on the link you received by email.</div>';
                    exit;
                }
                
                $user_id = mysqli_real_escape_string($link, $_GET['user_id']);
                $key = mysqli_real_escape_string($link, $_GET['key']);
                $time = time() - 86400;

                $stmt = $link->prepare("SELECT user_id FROM forgotpassword WHERE rkey = ? AND user_id = ? AND time > ? AND status = 'pending'");
                $stmt->bind_param("ssi", $key, $user_id, $time);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows !== 1) {
                    echo '<div class="alert alert-danger">Invalid or expired reset link. Please try again.</div>';
                    exit;
                }

                echo "
                <form method='post' id='passwordreset'>
                    <input type='hidden' name='key' value='$key'>
                    <input type='hidden' name='user_id' value='$user_id'>
                    <div class='form-group'>
                        <label for='password'>Enter your new Password:</label>
                        <input type='password' name='password' id='password' placeholder='Enter Password' class='form-control' required>
                    </div>
                    <div class='form-group'>
                        <label for='password2'>Re-enter Password:</label>
                        <input type='password' name='password2' id='password2' placeholder='Re-enter Password' class='form-control' required>
                    </div>
                    <input type='submit' name='resetpassword' class='btn btn-success btn-lg' value='Reset Password'>
                </form>";
                ?>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $("#passwordreset").submit(function(event) { 
        event.preventDefault();
        var datatopost = $(this).serializeArray();

        $.ajax({
            url: "storeresetpassword.php",
            type: "POST",
            data: datatopost,
            success: function(data) {
                $('#resultmessage').html(data);
            },
            error: function() {
                $("#resultmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            }
        });
    });
    </script>
</body>
</html>
