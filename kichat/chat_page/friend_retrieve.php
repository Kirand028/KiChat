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
        $my_userName = $row['username'];
        $my_email = $row['email'];
        
    }
    else {
        echo'<script>window.location.href="../login.html";</script>';
        exit();
    }
    
    // $friend_fetch = 'select * from friend where following_username ="" and following ';
    
    $all_query = "
    SELECT username, profile
    FROM signup
    WHERE username <> '$my_userName'
        AND username IN (
            SELECT f1.followee_username
            FROM friend f1
            WHERE f1.follower_username = '$my_userName'
            AND f1.followee_username IN (
                SELECT f2.follower_username
                FROM friend f2
                WHERE f2.followee_username = '$my_userName'
            )
        )
    ORDER BY username DESC";

    $res=$conn->query($all_query);
    
    while($res_row = $res->fetch_assoc()) {
    
    $name = $res_row['username'];
    $f_profile_photo = $res_row['profile'];
    
    if($f_profile_photo == "") {
        $f_profile_photo = substr($name, 0, 1);
    }
    else {
        $f_profile_photo = '<img id="pf" name="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($f_profile_photo) . '" alt="P">';
    }
    
    echo '<div class="user" id="user"> <div class="image">'.$f_profile_photo.'</div>
                <div class="user_content">
                    <div class="text">
                        <span class="name">'.$name.'</span>
                        <p class="username">@namedlorem</p>
                    </div>
                    <button class="follow" id="message_but" name="message_but" value="'.$name.'"><i class="fas fa-comments"></i></button>
                </div></div>';
                
    }
    
    
    
?>
