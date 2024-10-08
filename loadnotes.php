<?php
session_start();
include('connection.php');

// Get the user_id
$user_id = $_SESSION['user_id'];

// Delete empty notes
$sql = "DELETE FROM notes WHERE note = ''";
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '<div class="alert alert-warning">An error occurred!</div>'; 
    exit;
}

// Select notes for the user
$sql = "SELECT * FROM notes WHERE user_id = '$user_id' ORDER BY time DESC";

// Shows notes or alert message
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $note_id = htmlspecialchars($row['id']);
            $note = htmlspecialchars($row['note']);
            $time = $row['time'];
            $time = date("F d, Y h:i:s A", $time);
            
            echo "
            <div class='note'>
                <div class='col-xs-5 col-sm-3 delete'>
                    <button class='btn-lg btn-danger' style='width:100%' data-note-id='$note_id'>Delete</button>
                </div>
                <div class='noteheader' id='$note_id'>
                    <div class='text'>$note</div>
                    <div class='timetext'>$time</div>    
                </div>
            </div>";
        }
    } else {
        echo '<div class="alert alert-warning">You have not created any notes yet!</div>'; 
        exit;
    }
} else {
    echo '<div class="alert alert-warning">An error occurred!</div>'; 
    exit;
}
?>
