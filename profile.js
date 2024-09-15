$(function() {
    // Ajax call to updateusername.php
    $("#updateusernameform").submit(function(event) {
        event.preventDefault();
        var datatopost = $(this).serialize();
        $.ajax({
            url: "updateusername.php",
            type: "POST",
            data: datatopost,
            success: function(data) {
                if (data) {
                    $("#updateusernamemessage").html(data);
                } else {
                    location.reload();
                }
            },
            error: function() {
                $("#updateusernamemessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            }
        });
    });

    // Ajax call to updatepassword.php
    $("#updatepasswordform").submit(function(event) {
        event.preventDefault();
        var datatopost = $(this).serialize();
        $.ajax({
            url: "updatepassword.php",
            type: "POST",
            data: datatopost,
            success: function(data) {
                if (data) {
                    $("#updatepasswordmessage").html(data);
                } else {
                    // Optionally reset the form or redirect
                }
            },
            error: function() {
                $("#updatepasswordmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            }
        });
    });

    // Ajax call to updateemail.php
    $("#updateemailform").submit(function(event) {
        event.preventDefault();
        var datatopost = $(this).serialize();
        $.ajax({
            url: "updateemail.php",
            type: "POST",
            data: datatopost,
            success: function(data) {
                if (data) {
                    $("#updateemailmessage").html(data);
                } else {
                    // Optionally reset the form or redirect
                }
            },
            error: function() {
                $("#updateemailmessage").html("<div class='alert alert-danger'>There was an error with the Ajax Call. Please try again later.</div>");
            }
        });
    });
});
