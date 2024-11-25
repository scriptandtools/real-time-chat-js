<?php
// Ensure session is started
session_start();

// Include the database connection
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch users for sidebar search
$search = isset($_GET['query']) ? "%" . $_GET['query'] . "%" : "%";
$sql = "SELECT * FROM tbl_users WHERE id != ? AND username LIKE ? ORDER BY username ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $search); 
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle live search
if (isset($_GET['action']) && $_GET['action'] === 'live_search') {
    foreach ($users as $user) {
        echo '<a href="?chat_with=' . htmlspecialchars($user['id']) . '">
                <li class="group">
                    <div class="avatar"><img src="' . $user['image'] . '" alt=""></div>
                    <p class="GroupName">' . htmlspecialchars($user['username']) . '</p>
                    <p class="GroupDescrp">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                </li>
              </a>';
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'fetch_messages' && isset($_GET['chat_with'])) {
    $chat_with_id = intval($_GET['chat_with']);
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Query to fetch messages between the user and the chat partner
    $sql = "SELECT tbl_chat.message, tbl_chat.created_at, tbl_users.username, tbl_users.image, tbl_chat.sender_id
            FROM tbl_chat
            JOIN tbl_users ON tbl_chat.sender_id = tbl_users.id
            WHERE (tbl_chat.sender_id = ? AND tbl_chat.receiver_id = ?)
               OR (tbl_chat.sender_id = ? AND tbl_chat.receiver_id = ?)
            ORDER BY tbl_chat.created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user_id, $chat_with_id, $chat_with_id, $user_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Return the message HTML
    foreach ($messages as $msg) {
        echo '<div class="message ' . ($msg['sender_id'] === $user_id ? 'me' : 'you') . '">';
        echo '<p class="messageContent">' . htmlspecialchars($msg['message']) . '</p>';
        echo '<div class="messageDetails">';
        echo '<div class="messageTime">' . date("h:i A", strtotime($msg['created_at'])) . '</div>';
        echo '<i class="fa-solid fa-check"></i>';
        echo '</div>';
        echo '</div>';
    }
    exit;
}
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $chat_with_id = intval($_POST['chat_with']);
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id']; // Get the logged-in user ID

    if ($message && $chat_with_id) {
        $sql = "INSERT INTO tbl_chat (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $chat_with_id, $message);
        if ($stmt->execute()) {
            echo "Message sent!";
        }
        exit;
    }
}
// Check if 'chat_with' is set in the URL
if (isset($_GET['chat_with'])) {
    $chat_with_id = intval($_GET['chat_with']); // Get the ID of the user you're chatting with

    // Query to get the details of the user you're chatting with
    $sql = "SELECT * FROM tbl_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chat_with_id);
    $stmt->execute();
    $chat_with_user = $stmt->get_result()->fetch_assoc(); // Fetch user data
} else {
    $chat_with_user = null; // No user selected for chat
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <head>
    <!-- Socket.io Client Library -->
    <script src="https://cdn.socket.io/4.1.0/socket.io.min.js"></script>
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
    <style>
      
        @media (max-width:750px) {
            main{
                grid-template-columns: 0px 0px auto;
                width: 100%;
                height: 100%;
            }
          
        }
    </style>
</head>
<body>
    <main>
        <div class="sideNav1">
<a href="#"><li class="active"><i class="fa-regular fa-comment-dots"></i></li></a>
           <a href="phone.php">    <li><i class="fa-solid fa-phone"></i></li></a> 
        
            <a href="logout.php"><li><i class="fa-solid fa-gear"></i></li></a>
            <a href="delete.php"> <li><i class="fa-solid fa-trash-can"></i></li></a>
           <a href="fav.php">       <li><i class="fa-regular fa-star"></i></li></a>
     <a href="contact.php"> <li><i class="fa-solid fa-address-book"></i></li></a>
           
        </div>
        <div class="sideNav2" id="chat-numbers">
            <div class="SideNavhead">
                <h2>Chats</h2>
                <i class="fa-solid fa-filter"></i>
                <i class="fa-solid fa-user-plus"></i>

            </div>
            <div class="SearchInputHolder">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input class="searchInput" id="searchInput" placeholder="Search For Chat..">
</div>
<div id="search-results"></div> <!-- Container for live search results -->

            <?php foreach ($users as $user): ?>
    <a href="?chat_with=<?php echo $user['id']; ?>&query=<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
        <li class="group">
            <div class="avatar"><img src="<?php echo htmlspecialchars($user['image']); ?>" alt=""></div>
            <p class="GroupName"><?php echo htmlspecialchars($user['username']); ?></p>
            <p class="GroupDescrp">Lorem ipsum dolor sit amet consectetur adipisicing elit. Earujdsajf djf df dfjdkj
                dlkjfl.kjl dlkjf lkjlkdjfm, sequi.</p>
        </li>
    </a>
<?php endforeach; ?>

            
        </div>
        <section class="Chat" id="chats">
        <?php if ($chat_with_user): ?>
            <div class="ChatHead" style="height: 18%;">
                <li class="group">
                    <a href="chat.php" class="fa-solid fa-arrow-left"></a>
                    <div class="avatar"><img src="<?php echo htmlspecialchars($chat_with_user['image']); ?>"></div>
                    <p class="GroupName"><?php echo htmlspecialchars($chat_with_user['username']); ?></p>

                </li>
 

   
            </div>
            <div class="MessageContainer">
    <?php foreach ($messages as $msg): ?>
        <div class="message <?php echo $msg['sender_id'] === $_SESSION['user_id'] ? 'me' : 'you'; ?>">
            <div class="messageSeperator">Yesterday</div>
            <div class="message <?php echo $msg['sender_id'] === $_SESSION['user_id'] ? 'me' : 'you'; ?>">
                <p class="messageContent"><?php echo htmlspecialchars($msg['message']); ?></p>
                <div class="messageDetails">
                    <div class="messageTime"><?php echo date("h:i A", strtotime($msg['created_at'])); ?></div>
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<form id="MessageForm">
    <input type="text" name="message" id="MessageInput" placeholder="Type a message..." required>
    <button type="submit" class="Send"><i class="fa-solid fa-paper-plane"></i></button>
</form>

            <?php else: ?>
                <p class="no-chat-selected">Select a user to start chatting</p>
                <?php endif; ?>
        </section>
    </main>


</body>
</html>

  <script>
document.addEventListener("DOMContentLoaded", () => {
    const messageContainer = document.querySelector(".MessageContainer");
    const chatWith = new URLSearchParams(window.location.search).get("chat_with");

    // Fetch messages function
    function fetchMessages() {
        if (!chatWith) return;

        fetch(`chat.php?action=fetch_messages&chat_with=${chatWith}`)
            .then(response => response.text())
            .then(data => {
                messageContainer.innerHTML = data; // Update messages
                messageContainer.scrollTop = messageContainer.scrollHeight; // Scroll to bottom
            })
            .catch(console.error);
    }

    // Fetch messages every 2 seconds
    setInterval(fetchMessages, 1000);

    // Initial fetch
    fetchMessages();
});

document.getElementById("MessageForm").addEventListener("submit", (event) => {
    event.preventDefault();
    
    const messageInput = document.getElementById("MessageInput");
    const message = messageInput.value.trim();
    const chatWith = new URLSearchParams(window.location.search).get("chat_with");

    if (message && chatWith) {
        // Send the message to the server
        fetch("chat.php", {
            method: "POST",
            body: new URLSearchParams({
                action: "send_message",
                chat_with: chatWith,
                message: message
            })
        })
        .then(response => response.text())
        .then(data => {
            // Clear the input field
            messageInput.value = '';
            // Reload the messages
            fetchMessages();
        })
        .catch(console.error);
    }
});

</script>

