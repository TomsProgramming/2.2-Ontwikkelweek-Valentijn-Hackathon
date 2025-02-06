<?php
require '../config.php';
require 'assets/php/classes/chats.php';

if(!$loggedIn){
    echo '<script>window.location.href = "../login-register/login.php";</script>';
}

if($deviceData['emailVerified'] == 0){
    echo '<script>window.location.href = "../login-register/verification.php";</script>';
}

$selectChats = $conn->prepare("WITH LatestMessages AS (SELECT m.id, m.senderId, m.receiverId, m.message, m.readed, m.createdAt, CASE WHEN m.senderId = :userId THEN m.receiverId ELSE m.senderId END AS conversationPartner, ROW_NUMBER() OVER (PARTITION BY CASE WHEN m.senderId = :userId THEN m.receiverId ELSE m.senderId END ORDER BY m.createdAt DESC) AS row_num FROM messages m WHERE m.senderId = :userId OR m.receiverId = :userId) SELECT lm.id, lm.senderId, lm.receiverId, lm.message, lm.readed, lm.createdAt, u.username FROM LatestMessages lm JOIN users u ON u.id = (CASE WHEN lm.senderId = :userId THEN lm.receiverId ELSE lm.senderId END) WHERE lm.row_num = 1 ORDER BY lm.createdAt DESC;");
$selectChats->bindParam(':userId', $userData['id']);
$selectChats->execute();
$chats = $selectChats->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valentijn Hackathon - Chats</title>
    <link rel="stylesheet" href="assets/css/chats.css?v=<?php echo filemtime('assets/css/chats.css') ?>">
    <link rel="manifest" href="../manifest.json">
</head>

<body>
    <header class="nav-bar">
        <div class="nav-left">
            <div class="logo">
                <h1>Valentijn Hackathon</h1>
            </div>
        </div>

        <div class="nav-center">
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php">Love Tester</a></li>
                <li><a href="chats.php">Chats</a></li>
            </ul>
        </div>

        <div class="nav-right">
            <button class="menu-toggle">☰</button>
            <div class="profile-icon">
                <img src="../uploads/profilePictures/<?php echo $userData['id'] ?>.png" alt="Profiel">
            </div>
        </div>
    </header>

    <div class="container">

        <div class="mobile-header">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
        </div>

        <div class="sidebar">
            <div class="chat-list">
                <?php
                foreach ($chats as $chat){
                    $anthorUserId = intval($chat['senderId']) === intval($userData['id']) ? $chat['receiverId'] : $chat['senderId'];
                    $chatNotifications = chats::getChatNotifications($anthorUserId);
                ?>
                <div class="chat-item" onclick="openChatAndCloseSidebar(this)"
                    data-username="<?php echo $chat['username'] ?>">
                    <div class="header">
                        <img class="avatar" alt="Profile"
                            src="../uploads/profilePictures/<?php echo $anthorUserId ?>.png">
                        <span class="username"><?php echo $chat['username'] ?></span>
                        <span class="unreadedMessages"
                            style="display: <?php echo intval($chatNotifications) > 0 ? 'block' : 'none' ?>;">
                            <?php echo $chatNotifications ?>
                        </span>
                    </div>
                    <div class="content">
                        <span class="last-message"><?php echo $chat['message'] ?></span>
                    </div>
                    <div class="footer">
                        <span class="time"><?php echo $chat['createdAt'] ?></span>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
            <div class="sidebar-buttons">
                <button class="add-chat" onclick="openAddChatMenu()">+ Voeg chat toe</button>
            </div>
        </div>

        <div class="overlay" onclick="closeSidebar()"></div>

        <div class="chat-container">
            <div class="messages">
            </div>
            <div class="input-container">
                <input class="message" type="text" placeholder="Typ een bericht..." />
                <button class="send">Verzenden</button>
            </div>
        </div>
    </div>

    <div id="addChatMenu" class="add-chat-menu">
        <h3>Nieuwe chat toevoegen</h3>
        <input type="text" class="username" placeholder="Gebruikersnaam" />
        <button class="close" onclick="closeAddChatMenu()">Sluit</button>
        <button class="add-chat">Toevoegen</button>
    </div>

    <div id="notification-container"></div>
    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js') ?>;
    </script>
    <script src="assets/js/chats.js?v=<?php echo filemtime('assets/js/chats.js') ?>"></script>
</body>

</html>