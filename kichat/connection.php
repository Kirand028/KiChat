<?php

    $servername = "sql112.infinityfree.com";
    $username = "if0_35222739";
    $password = "orhd0KabLU";
    $dbname = "if0_35222739_ki_chatbd";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // $servername = "localhost";
    // $username = "id21145172_lyrik";
    // $password = "Login123&";
    // $dbname = "id21145172_lyrik";

    // $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
