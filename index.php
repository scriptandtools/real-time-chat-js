<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'fetch_users') {
        // Fetch users for sidebar search
        $search = isset($_GET['query']) ? "%" . $_GET['query'] . "%" : "%";
        $sql = "SELECT * FROM tbl_users WHERE id != ? AND username LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $_SESSION['user_id'], $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $response = $result->fetch_all(MYSQLI_ASSOC);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'fetch_messages') {
        // Fetch chat messages
        $chat_with_id = intval($_GET['chat_with']);
        $sql = "SELECT tbl_chat.message, tbl_chat.created_at, tbl_users.username, tbl_users.image, tbl_chat.sender_id
                FROM tbl_chat
                JOIN tbl_users ON tbl_chat.sender_id = tbl_users.id
                WHERE (tbl_chat.sender_id = ? AND tbl_chat.receiver_id = ?)
                   OR (tbl_chat.sender_id = ? AND tbl_chat.receiver_id = ?)
                ORDER BY tbl_chat.created_at ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $_SESSION['user_id'], $chat_with_id, $chat_with_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $response = $result->fetch_all(MYSQLI_ASSOC);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['receiver_id'])) {
    // Handle message submission
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO tbl_chat (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
        if ($stmt->execute()) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'error' => 'Message could not be sent'];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <title>Chat</title>
    <style>
        @media (max-width:750px) {
            main {
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
            <li class="active"><i class="fa-regular fa-comment-dots"></i></li>
            <li><i class="fa-solid fa-phone"></i></li>
            <li><i class="fa-solid fa-gear"></i></li>
            <li><i class="fa-solid fa-trash-can"></i></li>
            <li><i class="fa-regular fa-star"></i></li>
            <li><i class="fa-solid fa-address-book"></i></li>
        </div>
        <div class="sideNav2" id="chat-numbers">
            <div class="SideNavhead">
                <h2>Chats</h2>
                <i class="fa-solid fa-filter"></i>
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="SearchInputHolder">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input class="searchInput" placeholder="Search For Chat.." id="searchChat">
                <hr>
            </div>
            <ul id="userList"></ul>
        </div>
        <section class="Chat" id="chats">
            <div id="chatMessages"></div>
            <form id="messageForm">
                <input type="text" name="message" id="MessageInput" placeholder="Type a message..." required>
                <input type="hidden" id="receiverId">
                <button type="submit" class="Send"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </section>
    </main>

    <script>
        let selectedUser = null;

        function fetchUsers(query = '') {
            $.getJSON('chat.php', { action: 'fetch_users', query }, function (data) {
                const userList = $('#userList');
                userList.empty();
                data.forEach(user => {
                    userList.append(`
                        <li class="group" onclick="selectUser(${user.id}, '${user.username}', '${user.image}')">
                            <div class="avatar"><img src="${user.image}" alt=""></div>
                            <p class="GroupName">${user.username}</p>
                        </li>
                    `);
                });
            });
        }

        function fetchMessages(chatWithId) {
            $.getJSON('chat.php', { action: 'fetch_messages', chat_with: chatWithId }, function (data) {
                const chatMessages = $('#chatMessages');
                chatMessages.empty();
                data.forEach(msg => {
                    const alignClass = msg.sender_id === <?= $_SESSION['user_id'] ?> ? 'me' : 'you';
                    chatMessages.append(`
                        <div class="message ${alignClass}">
                            <p class="messageContent">${msg.message}</p>
                            <div class="messageDetails">
                                <div class="messageTime">${new Date(msg.created_at).toLocaleTimeString()}</div>
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </div>
                    `);
                });
            });
        }

        function selectUser(userId, username, image) {
            selectedUser = userId;
            $('#receiverId').val(userId);
            fetchMessages(userId);
        }

        $('#messageForm').submit(function (e) {
            e.preventDefault();
            const message = $('#MessageInput').val();
            const receiverId = $('#receiverId').val();

            if (message.trim() && receiverId) {
                $.post('chat.php', { message, receiver_id: receiverId }, function (data) {
                    if (data.success) {
                        fetchMessages(receiverId);
                        $('#MessageInput').val('');
                    }
                }, 'json');
            }
        });

        $('#searchChat').on('input', function () {
            const query = $(this).val();
            fetchUsers(query);
        });

        // Initialize
        fetchUsers();
    </script>
</body>
</html>
