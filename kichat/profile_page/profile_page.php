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
        $my_password = $row['password'];
        $my_fullname = $row['fullname'];
        $my_dob = $row['dob'];
        $my_bio = $row['bio'];
        $my_gender = $row['gender'];
        $my_profile = $row['profile'];
    }
    else {
        echo "<script>window.location.href='../login.html';</script>";
    }
    $message = '<i class="fas fa-smile"></i> Welcome back, ' . $my_userName;
    $if_msg = $message;
        
    if (isset($_POST['update'])) {
    
        $u_username = $_POST['username'];
        $u_email = $_POST['email'];
        $u_password = $_POST['password'];
        $u_fullname = $_POST['fullname'];
        $u_dob = $_POST['dob'];
        $u_bio = $_POST['bio'];
        $u_gender = $_POST['gender'];

        // Checking for no changes
        if (
            $my_userName === $u_username &&
            $my_email === $u_email &&
            $my_password === $u_password &&
            $my_fullname === $u_fullname &&
            $my_dob === $u_dob &&
            $my_bio === $u_bio &&
            $my_gender === $u_gender
        ) {
            $message = '<i class="fa fa-info-circle" style="color:red;"></i> No changes made';
        } 
        else {
                // Check if email or username has been changed
                $emailChanged = ($my_email !== $u_email);
                $usernameChanged = ($my_userName !== $u_username);
    
                // Check if the new username or email already exists
                if ($emailChanged) {
                    $result = $conn->query("SELECT * FROM signup WHERE LOWER(email) = LOWER('$u_email')");
                     if ($result->num_rows > 0) {
                        $message = '<i class="fa fa-info-circle" style="color:red;"></i> Email already in use';
                    }
                } 
                elseif ($usernameChanged) {
                $result = $conn->query("SELECT * FROM signup WHERE LOWER(username) = LOWER('$u_username')");
                    if ($result->num_rows > 0) {
                        $message = '<i class="fa fa-info-circle" style="color:red;"></i> Username is taken';
                    }
                }
    
                // Update the profile
                if ($if_msg == $message) {
                    
                    $updateFields = "password = '$u_password', fullname = '$u_fullname', dob = '$u_dob', bio = '$u_bio', gender = '$u_gender'";
                    $updateCondition = "email = '$my_email'";

                    if ($emailChanged || $usernameChanged) {
                        $updateFields .= ", username = '$u_username', email = '$u_email'";
                    }

                    $updateQuery = "UPDATE signup SET $updateFields WHERE $updateCondition";
                    $updateResult = $conn->query($updateQuery);

                    if ($updateResult) {
                    $message = '<i class="fa fa-check-circle" style="color:green;"></i> Success, wait 5sec to reflect';
                    $encode_email = base64_encode($u_email);
                    echo '<script>setTimeout(function() { window.location.href = "profile_page.php?user=' . $encode_email . '"; }, 3000);</script>';
                    } 
                    else {
                        $message = '<i class="fa fa-warning" style="color:red;"></i> Something went wrong';
                    }
                }
        }
    }
    elseif(isset($_POST['profile'])) {
        
        // echo '<script>alert("Profile");</script>';
        @$image = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
        
            
        $image_name = $_FILES["image"]["name"];
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
                    $image_name="";
				    $message = '<i class="fa fa-warning" style="color:red;"></i> Oops! Size is '.$size.'MB';
                }
                else
                {
                    $iqry = "select profile from signup where email ='".$decode_email."'";
                    $irslt = $conn->query($iqry);
                    if($irslt->num_rows > 0) {
				    
				        $uque="update signup set profile = '$image' where email = '$decode_email'";
                        if($uqr = $conn->query($uque)) {
                            
                            $image="";
                            $image_name="";
				            $message = '<i class="fa fa-check-circle" style="color:green;"></i> Profile set';
                            echo '<script>setTimeout(function() { window.location.href = "profile_page.php?user=' . $encode_email . '"; }, 2500);</script>';
                            
			            }
			            else {
			                $image="";
                            $image_name="";
				            $message = '<i class="fa fa-warning" style="color:red;"></i> Oops! Update error';
			            }    
                    }
                }
            }
            else
            {
                $image="";
                $image_name="";
				$message = '<i class="fa fa-warning" style="color:red;"></i> Oops! Invalid file';
            }
        }
        
    }

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/solid.min.css">
    <title>Profile Page</title>
</head>
<body>
    <header>
        <h3>KiChat</h3>
        <input type="text" value="<?php echo $my_userName; ?>" readonly>
    </header>

    <form method="post" autocomplete="off" enctype="multipart/form-data">
        <div class="profile-container">
            <div class="card">
                <h3>Your profile</h3>
                
                <div class="user-profile">
                    <div class="avatar">
                       <div class="his-img"></div> 
                        <input type="file" id="file-input" name="image" accept="image/*">
                        <button class="profile" name="profile">Submit</button>
                        <p><i class="fa fa-info-circle"></i> File should be jpg/jpeg/png and less than 1MB</p>
                    </div>
                </div>
                <div class="user-details">
                    <div class="mes-log">
                        <p id="message"><?php echo $message; ?></p>
                        <a href="..\login.html">logout &nbsp;<i class="fas fa-sign-out-alt"></i></a>
                    </div>
                    <div class="inputs">
                        <div class="input-container">
                            <label for="username">Username</label>
                            <input type="text" class="editable" name="username" value="<?php echo $my_userName; ?>" readonly required oninput="validateInput(event);">
                        </div>
                        <div class="input-container">
                            <label for="email">Email</label>
                            <input type="email" class="editable" name="email" value="<?php echo $my_email; ?>" readonly required oninput="validateInput(event);">
                        </div>
                        <div class="input-container">
                            <label for="password">Password</label>
                            <input type="text" class="editable" name="password" value="<?php echo $my_password; ?>" readonly required oninput="validateInput(event);">
                        </div>
                        <div class="input-container">
                            <label for="fullname">Full Name</label>
                            <input type="text" class="editable" name="fullname" value="<?php echo $my_fullname; ?>" readonly oninput="validatespaceInput(event);">
                        </div>
                        <div class="input-container">
                            <label for="dob">Birth Date</label>
                            <input type="date" class="editable" name="dob" value="<?php echo $my_dob; ?>" readonly>
                        </div>
                        <div class="input-container">
                            <label for="bio">Short Bio</label>
                            <input type="text" class="editable" name="bio" value="<?php echo $my_bio; ?>" readonly oninput="validatespaceInput(event);">
                        </div>
                        <div class="input-container">
                            <label for="gender">gender</label>
                            <select class="editable" id="sel" name="gender" disabled>
                                <option><?php echo $my_gender; ?></option>
                                <?php 
                                    if($my_gender == 'Male') {
                                        echo '<option>Female</option>';
                                        echo '<option>Unspecify</option>';
                                    }
                                    elseif($my_gender == 'Female') {
                                        echo ' <option>Male</option>';
                                        echo '<option>Unspecify</option>';
                                    }
                                    else {
                                        echo '<option>Male</option>';
                                        echo '<option>Female</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="update-button">
                        <button id="update" name="update" disabled>Save</button>&nbsp;&nbsp;&nbsp;&nbsp; 
                        <button id="edit" onclick="makeEditable();"><i class="fas fa-pen"></i></button>
                    </div>
                    
                </div>

            </div>
            
        </div>
    </form>
    
    <footer>
        <a href="../post_page/post_page.php?user=<?= $encode_email ?>"><i class="fas fa-pen"></i></a>
        <a href="../chat_page/message_page.php?user=<?= $encode_email ?>"><i class="fas fa-comments"></i></a>
        <a href="../home_page/home_page.php?user=<?= $encode_email ?>"><i class="fas fa-home"></i></a>
        <a href="../friends_page/friends_page.php?user=<?= $encode_email ?>"><i class="fas fa-user-friends"></i></a>
        <a class="active"><i class="fas fa-user"></i></a>
    </footer>
    
    <script>
        
        function validateInput(event) {
            const input = event.target;
            const value = input.value;
            const sanitizedValue = value.replace(/[^!#$%^,_&*+=@.a-zA-Z0-9]/g, '');
            input.value = sanitizedValue;
        }
        
        function validatespaceInput(event) {
            const input = event.target;
            const value = input.value;
            const sanitizedValue = value.replace(/[^!#$%^,_&*+=@.a-zA-Z0-9 ]/g, '');
            input.value = sanitizedValue;
        }
    
        function makeEditable() {
            
        document.getElementById("edit").disabled = true;
        document.getElementById("update").disabled = false;
        document.getElementById("sel").disabled = false;
        
        var inputBoxes = document.querySelectorAll('.editable'); // Select textboxes with class 'editable'
        for (var i = 0; i < inputBoxes.length; i++) {
                inputBoxes[i].readOnly = false;
                inputBoxes[i].style.borderBottom="2px solid blue";
            }
        }
        
        const hisImageDiv = document.querySelector('.his-img');

  // Create an img element
  const img = document.createElement('img');
  img.alt = 'profile';
  img.src = 'data:image/jpeg;base64,<?= base64_encode($my_profile) ?>';

  // Append the img element to the .his-image div
  if (hisImageDiv) {
    hisImageDiv.appendChild(img);
  } else {
    console.error('Element not found!');
  }
        
    </script>
    
</body>
</html>