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
    
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="message_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <title>Chat</title>
  
</head>
<body>
    <header>
        <h3>KiChat</h3>
        <input type="text" value="<?= $my_userName ?>" readonly>
    </header>

    <div class="container">
        
        <form method="POST" id="chat-form">
        <div class="chats" id="chats">
        
            <div class="friend-details" id="profile-name">
                <div class="user">
                    <input type="hidden" value="" id="inpHidden">
                    <div class="image"></div>
                    <div class="user_content">
                        <div class="text">
                                <span class="name"></span>
                                <p class="username"></p>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="chat-messages" id="chat-messages">
                <div class="chat-container" id="chat-container">
                    <p style="text-align:center; margin-top: 120px;" id="showLoading">Select the User to chat</p>
                </div>    
            </div>
            
            <div class="chat-inputs">
                <input type="text" name="chat_message" id="inputMessage" placeholder="Type Here..." oninput="validateInput(event);" required>
                <button name="send" id="send" disabled>
                    <div class="svg-wrapper-1">
                      <div class="svg-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                          <path fill="none" d="M0 0h24v24H0z"></path>
                          <path
                            fill="currentColor"
                            d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
                          ></path>
                        </svg>
                      </div>
                    </div>
                </button>
            </div>
        </div>
        </form>
        
        
        <div class="friends-list">
            <h2>Ping your buddies</h2>
            <form method="post" id="friend-form">
                
            
            </form>
        </div>
        
        
    </div>
    
    <footer>
        <a href="../post_page/post_page.php?user=<?= $encode_email ?>"><i class="fas fa-pen"></i></a>
        <a class="active"><i class="fas fa-comments"></i></a>
        <a href="../home_page/home_page.php?user=<?= $encode_email ?>"><i class="fas fa-home"></i></a>
        <a href="../friends_page/friends_page.php?user=<?= $encode_email ?>"><i class="fas fa-user-friends"></i></a>
        <a href="../profile_page/profile_page.php?user=<?= $encode_email ?>"><i class="fas fa-user"></i></a>
    </footer>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function validateInput(event) {
        const input = event.target;
        const value = input.value;
        const sanitizedValue = value.replace(/[^!#$%^,_&*+=@.a-zA-Z0-9 ]/g, '');
        input.value = sanitizedValue;
    }
        
    $(document).ready(function() {

    document.getElementById('send').disabled = true;
    document.getElementById('inputMessage').readOnly = true;
    
    var encodedEmail = '<?php echo $encode_email; ?>';
    $.ajax({
        type: 'POST',
        url: 'friend_retrieve.php?user=' + encodedEmail,
        data: {},
        success: function(response) {
            $('#friend-form').html(response);
        },
        error: function(xhr, status, error) {
            console.error(error);
            $('#friend-form').html('Error occurred while fetching.');
        }
    });
    
    $('#chat-form').on('click', '#send', function() {
        event.preventDefault();
        var sendto = $(this).val();
        var encodedEmail = '<?php echo $encode_email; ?>';
        let inputMessage = $('#inputMessage').val();
        $.ajax({
            type: 'POST',
            url: 'message_list.php?user=' + encodedEmail,
            data: { 
                send: sendto,
                chat_message: inputMessage
            },
            success: function(response) {
                $('#inputMessage').val('');
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#chats').html('Error occurred while fetching.');
            }
        });
    });
    
    function fetchChatHeader(buttonValue) {
            
        var encodedEmail = '<?php echo $encode_email; ?>';
        $.ajax({
            type: 'POST',
            url: 'message_list.php?user=' + encodedEmail,
            data: { 
                type: 'head',
                message_but: buttonValue
            },
            success: function(response) {
                $('#profile-name').html(response);
                $('#send').val(buttonValue);
                $('#send').prop('disabled', false);
                $('#inputMessage').prop('readonly', false);
            },
            error: function(xhr, status, error) {
                $('#profile-name').html('Error occurred while fetching.');
            }
        });
    }

    $('#friend-form').on('click', '#message_but', function() {
        event.preventDefault();
        var buttonValue = $(this).val();
        $('#inpHidden').val('');
        $('#chat-messages').html('<p style="text-align:center; margin-top: 120px;" id="showLoading">Loading...</p>');
        fetchChatHeader(buttonValue); 
    });
       
        
    var encodedEmail = '<?php echo $encode_email; ?>';
    setInterval(function() {
        let otherUser1 = $('#inpHidden').val();
        if(otherUser1 !== "") {
            $.ajax({
                type: 'POST',
                url: 'repeat_message.php?user=' + encodedEmail,
                data: { otherUser: otherUser1 },
                success: function(response) {
                    $('#chat-messages').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    $('#chat-messages').html('Error occurred while fetching.');
                }
            });
        }
    }, 1000);

});


    
</script>

</html>
