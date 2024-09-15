<?php
session_start();
include('connection.php');

// Get the id of the note and the content sent through Ajax
$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$note = filter_var($_POST['note'], FILTER_SANITIZE_STRING);

// Get the current time
$time = time();

// Prepare and bind
$stmt = $link->prepare("UPDATE notes SET note = ?, time = ? WHERE id = ?");
$stmt->bind_param("sii", $note, $time, $id);

// Execute the statement and check for errors
if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

// Close the statement
$stmt->close();

// Close the database connection
$link->close();
?>
