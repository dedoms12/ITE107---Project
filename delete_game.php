<?php
// delete_user.php

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['game_id']);

    // Perform the deletion in the database
    $deleteQuery = "DELETE FROM games WHERE game_id = '$id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        // You can perform additional actions if needed
        echo 'success';
    } else {
        echo 'error';
    }
}

// Close the database connection
mysqli_close($conn);
