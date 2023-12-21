<?php

    $servername = "YourServername";
    $username = "YourUsername";
    $password = "YourPassword";
    $dbname = "YouDatabaseName";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
