<?php
    
    include '../connection.php';

    @$encode_email = $_GET['user'];
    if ($encode_email == "") {
        echo "<script>window.location.href='../login.html';</script>";
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
        echo "<script>window.location.href='../login.html';</script>";
    }
    
    //query to fetch the the post  

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/solid.min.css">
    <title>Post Page</title>
</head>
<body>
    <header>
        <h3>KiChat</h3>
        <input type="text" value="<?php echo $my_userName; ?>" readonly>
    </header>

    <div class="post-container">
            <div class="old-post">
                <h4>Latest Posts <i class="fas fa-pen"></i></h4>
    
                <?php 
            
                    $post_query = "SELECT kpost.*, signup.profile AS user_profile 
                                    FROM kpost 
                                    LEFT JOIN signup ON kpost.username = signup.username 
                                    WHERE kpost.username <> '$my_userName' 
                                    ORDER BY kpost.post_date DESC, kpost.post_time DESC";
                    $post_rslt = $conn->query($post_query);
        
                    if ($post_rslt->num_rows < 1) {
                        echo '<p> <i class="fa fa-warning"></i> No posts to show</p>';
                    } 
                    else {
            
                        while ($post_row = $post_rslt->fetch_assoc()) {
                            
                            echo '<div class="card">';
                            
                            $post_id = $post_row['post_id'];
                            $post_username = $post_row['username'];
                            $post_date = $post_row['post_date'];
                            $post_time = $post_row['post_time'];
                            $post_message = $post_row['post_message'];
                            $post_image = $post_row['post_image'];
                            $user_profile = $post_row['user_profile'];
                ?>            
                    <div class="info">
                        <span class="avatar"><?php if($user_profile != "") { echo '<img id="pf" class="post-img-placeholder" data-img="data:image/jpeg;base64,' . base64_encode($user_profile) . '" alt="P">'; }else{ echo substr($post_username, 0, 1); }?></span>
                        <div class="name-and-date">
                            <span class="his-name"><?= $post_username ?></span>
                            <span class="post-date"><i class="fas fa-globe"></i> <?= $post_date ?></span>
                        </div>
                    </div>
                    <div class="post-img-caption">    
                        <p class="caption"><?= $post_message ?></p>

                        <?php
            if ($post_image != "") {

                echo '<img class="post-img-placeholder" id="post_img" data-img="data:image/jpeg;base64,' . base64_encode($post_image) . '" alt="Post">'; 
            }
            ?>  
                        <div class="post-time"><span>Posted on KiChat</span><span><?= $post_time ?></span></div>
                    </div>
                
        <?php echo '</div>';
        } 
        }
        ?>
        

                
            </div>
    </div>

    <footer>
        <a href="../post_page/post_page.php?user=<?= $encode_email ?>"><i class="fas fa-pen"></i></a>
        <a href="../chat_page/message_page.php?user=<?= $encode_email ?>"><i class="fas fa-comments"></i></a>
        <a class="active"><i class="fas fa-home"></i></a>
        <a href="../friends_page/friends_page.php?user=<?= $encode_email ?>"><i class="fas fa-user-friends"></i></a>
        <a href="../profile_page/profile_page.php?user=<?= $encode_email ?>"><i class="fas fa-user"></i></a>
    </footer>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const imagePlaceholders = document.querySelectorAll(".post-img-placeholder");
            imagePlaceholders.forEach(function(placeholder) {
                placeholder.setAttribute("src", placeholder.getAttribute("data-img"));
            });
        });    
    </script>
    
</body>
</html>
