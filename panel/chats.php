<?php
require '../config.php';
require 'assets/php/classes/chats.php';

$title = 'Valentijn Hackathon - Chats';

if(!$loggedIn){
    echo '<script>window.location.href = "../login-register/login.php";</script>';
    exit;
}

if($deviceData['emailVerified'] == 0){
    echo '<script>window.location.href = "../login-register/verification.php";</script>';
    exit;
}

$chatsData = chats::getChats(true);
if($chatsData['success'] === true){
    $chats = $chatsData['chats'];
}else{
    $chats = [];
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/chats.css?v=<?php echo filemtime('assets/css/chats.css') ?>">
</head>

<body>
    <?php include 'includes/navHeader.php'; ?>

    <div class="container">
        <div class="mobile-header">
            <button class="menu-toggle">☰</button>
        </div>

        <div class="sidebar">
            <div class="chat-list">
                <?php
                foreach ($chats as $chat){
                    $anthorUserId = intval($chat['senderId']) === intval($userData['id']) ? $chat['receiverId'] : $chat['senderId'];
                    $chatNotifications = chats::getChatNotifications($anthorUserId);
                ?>
                <div class="chat-item" onclick="mobileHeader.openChatAndClose(this)" data-username="<?php echo $chat['username'] ?>">
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
                        <?php 
                        if($chat['isLoveTesterResult'] === 1){ 
                            $loveTesterData = json_decode($chat['message'], true);
                        ?>
                            <span class="last-message"><?php echo $loveTesterData['name1'] . ' &amp; ' . $loveTesterData['name2'] . ' - ' . $loveTesterData['percentage'] . '%'; ?></span>
                        <?php }else{ ?>
                            <span class="last-message"><?php echo $chat['message'] ?></span>
                        <?php } ?>
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
                <button class="add-chat">+ Voeg chat toe</button>
            </div>
        </div>

        <div class="overlay" id="sideBarOverlay" onclick="closeSidebar()"></div>

        <div class="chat-container">
            <div class="messages">
            </div>
            <form class="input-container">
                <input class="message" type="text" placeholder="Typ een bericht..." />
                <button type="submit" class="send">Verzenden</button>
            </form>
        </div>
    </div>

    <div id="addChatMenu" class="add-chat-menu">
        <h3>Nieuwe chat toevoegen</h3>
        <input type="text" class="username" placeholder="Gebruikersnaam" />
        <button class="close">Sluit</button>
        <button class="add-chat">Toevoegen</button>
    </div>

    <div class="overlay" id="addChatMenuOverlay"></div>

    <div id="notification-container"></div>
    
    <?php include 'includes/videoShowcase.php'; ?>
    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js') ?>;
    </script>
    <script src="assets/js/chats.js?v=<?php echo filemtime('assets/js/chats.js') ?>"></script>
</body>

</html>