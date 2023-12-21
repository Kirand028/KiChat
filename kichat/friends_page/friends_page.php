<?php

    include '../connection.php';
    
    @$encode_email = $_GET['user'];
    if ($encode_email == "") {
        header("Location: ../login.html"); // Redirect using header
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
        $my_profile = $row['profile'];
        
    }
    else {
        header("Location: ../login.html"); // Redirect using header
        exit();
    }
    
    //follow information
    $following_count = "select count(*) as following_count from friend where follower_username='$my_userName'";
    $follower_count = "select count(*) as follower_count from friend where followee_username='$my_userName'";
    
    $a = $conn->query($following_count);
    $b = $conn->query($follower_count);
    
    if($a || $b) {
        $c = $a->fetch_assoc();
        $op_following_count = $c['following_count'];
        $d = $b->fetch_assoc();
        $op_follower_count = $d['follower_count'];
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="friends_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/solid.min.css">
    <title>Friends Page</title>
  
</head>
<body>
    <header>
        <h3>KiChat</h3>
        <input type="text" value="<?php echo $my_userName; ?>" readonly>
    </header>

    <div class="friends-container">
        <div class="recent-users">
            <div class="follow-details">
                <div class="his-image"></div>
                <div class="followers">
                    <span><i class="fa fa-user-friends"><sub>followers</sub></i>&nbsp;&nbsp; <?php echo $op_follower_count; ?></span><span><i class="fa fa-user-friends" id="mirroredIcon"><sub>following</sub></i>&nbsp;&nbsp; <?php echo $op_following_count; ?>
                    </span>
                </div>
            </div>
            <div class="top-head">Top 3 Accounts</div>
            <?php 
            
                $top_accounts = "SELECT followee_username, COUNT(*) AS follower_count 
                                 FROM friend 
                                 GROUP BY followee_username 
                                 ORDER BY follower_count DESC 
                                 LIMIT 3";
                $top_result = $conn->query($top_accounts);
                if($top_result->num_rows > 0) {
                    $i = 1;
                        while($top_rows = $top_result->fetch_assoc()) {
                            
                            echo'<div class="profile-card">';
                                echo'<section class="avatar">';
                                    
                                    // top 3 accounts profile fetch 
                                    $profile_photo = "";
                                    $profile_username = $top_rows['followee_username'];
                                    $top3_query = "select profile from signup where username='$profile_username'";
                                    $top3_result = $conn->query($top3_query);
                                    if($top3_result->num_rows > 0) {
                                        $top3_rows = $top3_result->fetch_assoc();
                                        $profile_photo = $top3_rows['profile'];
                                    }
                                    if($profile_photo != "") {
                                        echo '<img id="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($profile_photo) . '" alt="P">';
                                    }
                                    else {
                                        $profile_photo = substr($profile_photo, 0, 1);
                                        echo $profile_photo;
                                    }
                                    echo '<p>Top '.$i++.'</p>';
                                    echo '<i class="fas fa-trophy"></i>';
    
                                echo'</section>';
                
                                echo'<section class="user-info">';
                                
                                    echo'<input type="text" class="name" value="'.$top_rows['followee_username'].'" readonly>';
                                    echo'<input type="text" class="name" value="'.$top_rows['follower_count'].'" readonly>';
                                    
                                echo'</section>';
                            echo'</div>';
                        }   
                    }
            ?>
            
        </div>        
        <div class="all-users">
            <form method="POST" id="friend-form">
            <div class="card">
                <div class="follower-button-list">
                    <button class="follower-button" name="follower">Follower</button>
                    <button class="following-button" name="following">Following</button>
                    <button class="all-button" name="all">All</button>
                </div>
                <p class="title">Connect with Friends <i class="fa fa-user-friends"></i></p>
                <div class="user_container" id="user_container">
                    
                    <?php

                        //if all button clicked
                        if(isset($_POST['all'])) {
                         
                            // Fetch user details
                            
                            $all_query = "SELECT username, bio, profile FROM signup WHERE username <> '$my_userName'
                                            AND 
                                            username NOT IN (SELECT f1.followee_username FROM friend f1 
                                                WHERE f1.follower_username = '$my_userName')
                                            OR username IN (SELECT f2.follower_username FROM friend f2 
                                                WHERE f2.followee_username = '$my_userName'
                                                AND f2.follower_username NOT IN (SELECT f3.followee_username 
                                                    FROM friend f3 WHERE f3.follower_username = '$my_userName')
                                                )
                                            ORDER BY username DESC;";
                            
                            $all_result = $conn->query($all_query);
                    
                            if ($all_result->num_rows > 0 ) {
                                            
                                while($all_row = $all_result->fetch_assoc()) {
                                    
                                    $friend_username = $all_row['username'];
                                    $friend_bio = $all_row['bio'];
                                    $friend_profile = $all_row['profile'];
                                    
                                    echo '<div class="user">';
                                            
                                    if(strlen($friend_bio) > 25){ 
                                        $friend_bio = substr($friend_bio, 0, 27);
                                        $friend_bio = $friend_bio . " ...";
                                    }
                                    elseif($friend_bio == "") {
                                        $friend_bio = "@" . $friend_username;
                                    }
                                    
                                    echo '<div class="friendI">';
                        
                                    if ($friend_profile != "") {
                                        echo '<img id="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($friend_profile) . '" alt="P">';
                                    } 
                                    else {
                                        echo substr($friend_username, 0, 1);
                                    }
                                            
                                    echo '</div>';
                                            
                                    echo '<div class="user_content">';
                                    echo'<div class="text">';
                                    echo'<span class="name">'.$friend_username.'</span>';
                                    echo'<p class="username"> '.$friend_bio.'</p>';
                                    echo'</div>';
                                    echo'<button class="follow" name="follow_list" value="'.$friend_username.'">Follow</button>';
                                    echo'</div>';
                                    echo'</div>';
                                }
                            }
                            else { 
                                echo '<p style="text-align:center; color:#b3b3b3;margin:50px 0;"><i class="fa fa-warning"></i>&nbsp;&nbsp;Other friends other than Your friends</p>';
                            } 
                        }
                        // Add button click in all div
                        elseif(isset($_POST['follow_list'])) {
                            
                            $self_username = $my_userName;
                            $clicked_username = $_POST['follow_list'];
                            
                            $avoid_re_enter = "select follower_username, followee_username from friend where follower_username='$self_username' and followee_username='$clicked_username'";
                            $avoid_result = $conn->query($avoid_re_enter);
                            
                            if($avoid_result->num_rows == 0) {
                                $insert_list = "insert into friend(follower_username,followee_username) values('$my_userName','$clicked_username')";
                                $conn->query($insert_list);
                            }
                        }
                        //following list, list of a users that he is following
                        elseif(isset($_POST['following'])) {
                            
                            // Fetch user details
                            $subquery = "SELECT followee_username FROM friend WHERE follower_username = '$my_userName'";
                            $following_query = "SELECT username, bio, profile FROM signup WHERE username IN ($subquery) ORDER BY username DESC";
                            
                            $following_result = $conn->query($following_query);
                            
                            if($following_result->num_rows > 0) {
                                            
                                while($following_row = $following_result->fetch_assoc()) {
                                    
                                    $friend_username = $following_row['username'];
                                    $friend_bio = $following_row['bio'];
                                    $friend_profile = $following_row['profile'];
                                    echo '<div class="user">';
                                            
                                    $null_bio = "@" . $friend_username;
                                            
                                    if(strlen($friend_bio) > 25){ 
                                        $friend_bio = substr($friend_bio, 0, 30);
                                        $friend_bio = ($friend_bio === "") ? $null_bio : $friend_bio . " ...";
                                    }
                                            
                                    echo '<div class="friendI">';
                        
                                    if ($friend_profile != "") {
                                        echo '<img id="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($friend_profile) . '" alt="P">';
                                    } 
                                    else {
                                        echo substr($friend_username, 0, 1);
                                    }
                                            
                                    echo '</div>';
                                            
                                            
                                    echo '<div class="user_content">';
                                    echo'<div class="text">';
                                    echo'<span class="name">'.$friend_username.'</span>';
                                    echo'<p class="username"> '.$friend_bio.'</p>';
                                    echo'</div>';
                                    echo'<button class="follow" name="unfollow_list" value="'.$friend_username.'">Unfollow</button>';
                                    echo'</div>';
                                    echo'</div>';
                                }
                            }
                            else { 
                                echo '<p style="text-align:center; color:#b3b3b3;margin:50px 0;"><i class="fa fa-warning"></i>&nbsp;&nbsp;You are not following any one friends</p>';
                            } 
                        }
                        // unfollow button clicked in following div
                        elseif(isset($_POST['unfollow_list'])) {
                            
                            $self_username = $my_userName;
                            $clicked_username = $_POST['unfollow_list'];
                            
                            $avoid_re_delete = "select follower_username, followee_username from friend where follower_username='$self_username' and followee_username='$clicked_username'";
                            $avoid_result = $conn->query($avoid_re_delete);
                            
                            if($avoid_result->num_rows == 1) {
                                $delete_list = "delete from friend where follower_username ='$self_username' and followee_username ='$clicked_username'";
                                $conn->query($delete_list);
                            }
                            
                        }
                        // by default shows his follower
                        else {
                            // Fetch user details
                            $subquery = "SELECT follower_username FROM friend WHERE followee_username = '$my_userName'";
                            $follower_query = "SELECT username, bio, profile FROM signup 
                                         WHERE username IN ($subquery) ORDER BY username DESC";
                                         
                            $follower_result = $conn->query($follower_query);
                    
                            if ($follower_result->num_rows > 0 ) {
                                            
                                while($follower_row = $follower_result->fetch_assoc()) {
                                    
                                    $friend_username = $follower_row['username'];
                                    $friend_bio = $follower_row['bio'];
                                    $friend_profile = $follower_row['profile'];
                                    echo '<div class="user">';
                                            
                                    $null_bio = "@" . $friend_username;
                                            
                                    if(strlen($friend_bio) > 25){ 
                                        $friend_bio = substr($friend_bio, 0, 30);
                                        $friend_bio = ($friend_bio === "") ? $null_bio : $friend_bio . " ...";
                                    }
                                            
                                    echo '<div class="friendI">';
                        
                                    if ($friend_profile != "") {
                                        echo '<img id="pf" class="post-img-placeholder" src="data:image/jpeg;base64,' . base64_encode($friend_profile) . '" alt="P">';
                                    } 
                                    else {
                                        echo substr($friend_username, 0, 1);
                                    }
                                            
                                    echo '</div>';
                                            
                                            
                                    echo '<div class="user_content">';
                                    echo'<div class="text">';
                                    echo'<span class="name">'.$friend_username.'</span>';
                                    echo'<p class="username"> '.$friend_bio.'</p>';
                                    echo'</div>';
                                    
                                    //whether to give him the follow button or not or checking they are mutually following
                                    $button_allow = "select follower_username, followee_username from friend where follower_username='$my_userName' and followee_username='$friend_username'";
                                    $result_button_allow = $conn->query($button_allow);
                                    if($result_button_allow->num_rows === 0) {                                    
                                        echo'<button class="follow" name="follow_list" value="'.$friend_username.'">Follow back</button>';
                                    }
                                    else {
                                        echo'<button class="mutual_follow" disabled>Mutual</button>';
                                    }
                                    echo'</div>';
                                    echo'</div>';
                                }
                            }
                            else { 
                                echo '<p style="text-align:center; color:#b3b3b3;margin:50px 0;"><i class="fa fa-warning"></i>&nbsp;&nbsp; You have No followers</p>';
                            } 
                        }
                        
                    ?>    
                    
                </div>
            </div>
            </form>       

        </div>

    </div>

    <footer>
        <a href="../post_page/post_page.php?user=<?= $encode_email ?>"><i class="fas fa-pen"></i></a>
        <a href="../chat_page/message_page.php?user=<?= $encode_email ?>"><i class="fas fa-comments"></i></a>
        <a href="../home_page/home_page.php?user=<?= $encode_email ?>"><i class="fas fa-home"></i></a>
        <a class="active"><i class="fas fa-user-friends"></i></a>
        <a href="../profile_page/profile_page.php?user=<?= $encode_email ?>"><i class="fas fa-user"></i></a>
    </footer>
        
    <script>
      
        const hisImageDiv = document.querySelector('.his-image');
        // Create an img element
        const img = document.createElement('img');
        img.alt = 'No Profile';
        img.src = 'data:image/jpeg;base64,<?= base64_encode($my_profile) ?>';
    
        // Append the img element to the .his-image div
        if (hisImageDiv) {
            hisImageDiv.appendChild(img);
        }
        
    </script>
    
    
</body>
</html>