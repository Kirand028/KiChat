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
    
        
        
        $new = $_POST['otherUser'];
        $chat_query = "select * from kichat where (outgoing_username='$self_userName' and incoming_username='$new') or (outgoing_username='$new' and incoming_username='$self_userName')";
        
        $reslt_chat = $conn->query($chat_query);
        
            if($reslt_chat->num_rows > 0) {        
                echo '<div class="chat-container" id="chat-container">';
                    while ($row_chat = $reslt_chat->fetch_assoc()) {
                    
                        $chat = $row_chat['chat'];
                        $sender = $row_chat['outgoing_username'];
                    
                        // Check if the message sender is you or your friend
                        $messageClass = ($sender === $self_userName) ? 'my-chat' : 'friend-chat';
                    
                        // Output the message with its corresponding class
                        echo '<div class="' . $messageClass . '">' . $chat . '</div>';
                    }
                echo '</div>';
            }
            else {
                echo '<div class="chat-container" id="chat-container">';
                echo '<p class="no-message" style="text-align:center; margin-top:130px;">No message here yet, Say Hi.</p>';
                echo '</div>';
            }
    
?>
<html>
    <body>
        <script>
            var chatContainer = document.getElementById('chat-messages');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        </script>
    </body>
</html>