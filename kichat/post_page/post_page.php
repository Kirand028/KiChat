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
        $my_profile = $row['profile'];
    }
    else {
        echo "<script>window.location.href='../login.html';</script>";
    }
    
    
    $message = '<i class="fas fa-pen" style="color:#2900c5;"></i> Post something..';
        
    if (isset($_POST['post'])) {
    
        @$u_message = $_POST['post_message']; 
        @$image = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
        
        $today = date("Y-m-d");
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date("h:i:s A");
        
        
        if($u_message == "" && $image == "") {
           
            $message = '<i class="fa fa-warning" style="color:red;"></i> Write something';
        }
        elseif($u_message != "" && $image == "") {
            
            $mu_query = "insert into kpost(username, post_message, post_date, post_time) values('$my_userName','$u_message','$today','$currentTime')";
            $mu_rslt = $conn->query($mu_query);
            if($mu_rslt) {
                $message = '<i class="fas fa-handshake" style="color:#00c500;"></i> Posted';
                echo '<script>setTimeout(function() { window.location.href = "post_page.php?user=' . $encode_email . '"; }, 1000);</script>';
            }
            else {
                $message = '<i class="fa fa-warning" style="color:red;"></i> Something went wrong';
            }
        }
        elseif(($u_message == "" && $image != "") || ($u_message != "" && $image != "")) {
            
            $image_type=$_FILES["image"]["type"];
        
            $size=$_FILES["image"]["size"]/1024;
            $size=$size/1024;
            if($image == "") {
                
                $message = '<i class="fa fa-warning" style="color:red;"></i> Select an image file';
            }
            else {
            
                if($image_type=="image/png"||$image_type=="image/jpg"||$image_type=="image/jpeg") {

                    if($size > 1) {
                        $size = number_format($size,2);
                        $image="";
				        $message = '<i class="fa fa-warning" style="color:red;"></i> Oops! Size is '.$size.'MB. Should be less than 1MB';
                    }
                    else {
                        
                        $date="";
                        $iu_query = "insert into kpost(username, post_message, post_date, post_time, post_image) values('$my_userName','$u_message','$today','$currentTime','$image')";
                        $iu_rslt = $conn->query($iu_query);
                        if($iu_rslt) {
                            $message = '<i class="fas fa-handshake style="color:#00c500;"></i> Posted';
                            echo '<script>setTimeout(function() { window.location.href = "post_page.php?user=' . $encode_email . '"; }, 2000);</script>';
                        }
                        else {
                            $message = '<i class="fa fa-warning" style="color:red;"></i> Something went wrong';
                        }
                    }
                }
                else
                {
                    $image="";
				    $message = '<i class="fa fa-warning" style="color:red;"></i> Oops! Invalid file';
                }
            }
            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="post_write.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/solid.min.css">
    <title>Create Post Page</title>
  
</head>
<body>
    <header>
        <h3>KiChat</h3>
        <input type="text" value="<?php echo $my_userName; ?>" readonly>
    </header>

    <div class="post-container">
        

        <div class="post-content">
        <form method="post" autocomplete="off" enctype="multipart/form-data">
            <p class="message"><?php echo $message; ?></p>
            <textarea class="post-message" name="post_message" id="post-message" oninput="validateInput(event);" placeholder="Write something here.."></textarea>

            <label for="file-input" class="drop-container" title="Select .img , .jpg/jpeg , png files only.">
                <span class="drop-title" title="Select .img , .jpg/jpeg , png files only.">Drop Image files here</span>
                or
                <input type="file" accept="image/*" name="image"  id="file-input" title="Select .img , .jpg/jpeg , png files only.">
            </label>
            
            <p class="instruction"><i class="fa fa-info-cirlce"></i> File size upto 1MB and extension should be .img , .jpg/jpeg , png</p>
            <button class="post-button" name="post">Post <i class="fas fa-share"></i></button>
        </form>
        </div>
        <div class="old-post">
            <h4>Your Recent Posts</h4>
                
            <?php 

            
            $post_query = "SELECT * FROM kpost WHERE username = '$my_userName' ORDER BY post_date DESC, post_time DESC";
            $post_rslt = $conn->query($post_query);

            if ($post_rslt->num_rows < 1) {
                echo '<p class="fa fa-warning">No posts to show</p>';
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
            ?>
        <div class="info">
            <span class="avatar"><?php echo '<img id="pf" class="post-img-placeholder" data-img="data:image/jpeg;base64,' . base64_encode($my_profile) . '" alt="P">'; ?></span>
            <div class="name-and-date">
                <span class="his-name"><?= $post_username ?></span>
                <span class="post-date"><i class="fas fa-globe"></i> <?= $post_date . "  " . $post_time ?></span>
            </div>
        </div>
        <p class="caption"><?= $post_message ?></p>
        <?php
            if ($post_image != "") {

                echo '<img class="post-img-placeholder" id="post_img" data-img="data:image/jpeg;base64,' . base64_encode($post_image) . '" alt="Post">'; 
            }
            echo '</div>';
        } 
        }
        ?>

                
            
        </div>

    </div>
    
    <footer>
        <a class="active"><i class="fas fa-pen"></i></a>
        <a href="../chat_page/message_page.php?user=<?= $encode_email ?>"><i class="fas fa-comments"></i></a>
        <a href="../home_page/home_page.php?user=<?= $encode_email ?>"><i class="fas fa-home"></i></a>
        <a href="../friends_page/friends_page.php?user=<?= $encode_email ?>"><i class="fas fa-user-friends"></i></a>
        <a href="../profile_page/profile_page.php?user=<?= $encode_email ?>"><i class="fas fa-user"></i></a>
    </footer>
    
    <script>
        function validateInput(event) {
            const input = event.target;
            const value = input.value;
            const sanitizedValue = value.replace(/[^!#$%^,_&*+=@.a-zA-Z0-9 ]/g, '');
            input.value = sanitizedValue;
        }       
        
        document.addEventListener("DOMContentLoaded", function() {
            const imagePlaceholders = document.querySelectorAll(".post-img-placeholder");
            imagePlaceholders.forEach(function(placeholder) {
                placeholder.setAttribute("src", placeholder.getAttribute("data-img"));
            });
        });
        
    </script>
    
</body>
</html>
