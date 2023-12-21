<?php
include 'connection.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $existence = "SELECT username, email, password FROM signup WHERE LOWER(username) = LOWER('$username') OR LOWER(email) = LOWER('$username')";
    $result_existence = $conn->query($existence);

    if ($result_existence->num_rows > 0) {
        $row = $result_existence->fetch_assoc();
        $my_userName = $row['username'];
        $my_email = $row['email'];
        $my_password = $row['password'];

        if (strtolower($username) === strtolower($my_userName) || strtolower($username) === strtolower($my_email)) {
            if ($password === $my_password) {
            
                // Encoding the email
                $encode_email = base64_encode($my_email);
                echo '<i class="fa fa-check-circle"></i> success.' . $encode_email; // Concatenate the success signal and encoded email
                exit;
            } else {
                
                echo '<i class="fa fa-warning"></i> Wrong password.';
                exit;
            }
        }
    }
    else {
        
        echo '<i class="fa fa-warning"></i> Email/Username is not registered.';
        exit;
    }
}

echo 'Invalid request.';
$conn->close();
?>
