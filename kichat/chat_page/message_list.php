<?php

    include '../connection.php';
    
    @$encode_email = $_GET['user'];
    if ($encode_email == "") {
        echo'<script>window.location.href="../login.html";</script>';
        exit();
    }

    // Decode the email
    @$decode_email = base64_decode($encode_email);
    
    // Fetch user details
    $query = "SELECT * FROM signup WHERE email = '$decode_email' OR username = '$decode_email'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $self_userName = $row['username'];
    }
    else {
        echo'<script>window.location.href="../login.html";</script>';
        exit();
    }
    
    
    if(isset($_POST['message_but'])) {
        
        $buttonValue = $_POST['message_but'];
        
        // fetching user profile
        $selected_userProfile = "select profile from signup where username='$buttonValue'";
        $rslt_userProfile = $conn->query($selected_userProfile);
        $row_userProfile = $rslt_userProfile->fetch_assoc();
        $profile_photo = $row_userProfile['profile'];
        
        if($profile_photo == ""){
            $profile_photo = substr($buttonValue, 0, 1); 
        }
        else {
            $profile_photo = '<img id="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($profile_photo) . '" alt="P">';
        }
        
        if($_POST['type'] == 'head') {
                
                echo '<div class="user" id="user-div">
                    <input type="hidden" id="inpHidden" value="'.$buttonValue.'">
                    <div class="image">'.$profile_photo.'</div>
                    <div class="user_content">
                        <div class="text">
                                <span class="name" name="other" id="otherUser">'.$buttonValue.'</span>
                                <p class="username">@'.$buttonValue.'</p>
                        </div>
                    </div>
                </div>';
        }    
        
    }
    elseif(isset($_POST['send'])) {
        
        $chat_message = $_POST['chat_message'];
        $other_user = $_POST['send'];
        $chat_insert = "insert into kichat(outgoing_username, incoming_username, chat) values('$self_userName','$other_user','$chat_message')";
        
        if($chat_message !== ""){
            if($conn->query($chat_insert)) {
                $flag = 'Success';
            }
        }
    }
    
?>
