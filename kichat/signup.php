<?php
include 'connection.php';

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['cpassword'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Encoding the email
    $encode_email = base64_encode($email);

    $existence1 = "SELECT username, email FROM signup WHERE LOWER(username) = LOWER('$username')";
    $existence2 = "SELECT username, email FROM signup WHERE LOWER(email) = LOWER('$email')";
    $result_existence1 = $conn->query($existence1);
    $result_existence2 = $conn->query($existence2);

    if ($result_existence1->num_rows > 0) {
        
        echo '<i class="fa fa-info-circle"></i>  Username is Taken.';
    } elseif ($result_existence2->num_rows > 0) {
        
        echo '<i class="fa fa-info-circle"></i> Email already in use.';
    } 
    elseif($password !== $cpassword) {
        echo '<i class="fa fa-warning"></i> Both password should match.';
    }
    elseif(strlen($password) < 6 && strlen($cpassword) < 6 ) {
        echo '<i class="fa fa-info-circle"></i> Password must be atleatst 6 character long.';
    }
    elseif(ctype_alpha($password) && ctype_digit($password)) {
        echo '<i class="fa fa-info-circle"></i> Password should contain both alphabets and numbers.';
    }
    else {
        
        $gender = "Unspecify";
        $register = "INSERT INTO signup(username, email, password, gender) VALUES('$username', '$email', '$password', '$gender')";
        $result_register = $conn->query($register);

        if (!$result_register) {
            
            echo '<i class="fa fa-warning"></i>  Something went wrong.';
            exit;
        } else {
            
            // echo '<i class="fa fa-check-circle"></i> Registered, Redirecting to profile';
            echo " <script> 
                            window.location.href = 'profile_page/profile_page.php?user=".$encode_email."'; 
            </script>";
        }
    }
}
// setTimeout(function() {                                }}, 5000);
$conn->close();
?>
