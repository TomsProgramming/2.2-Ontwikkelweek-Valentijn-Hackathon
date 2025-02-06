<?php
require '../config.php';

if(!$loggedIn){
    echo '<script>window.location.href = "../login-register/login.php";</script>';
    exit;
}

if($deviceData['emailVerified'] == 0){
    echo '<script>window.location.href = "../login-register/verification.php";</script>';
    exit;
}

$selectHisotry = $conn->prepare("SELECT * FROM loveTesterHistory WHERE userId = :userId ORDER BY createdAt DESC");
$selectHisotry->bindParam(':userId', $userData['id']);
$selectHisotry->execute();
$history = $selectHisotry->fetchAll();
$historyCount = $selectHisotry->rowCount();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valentijn Hackathon - Love Tester</title>
    <link rel="stylesheet" href="assets/css/chats.css?v=<?php echo filemtime('assets/css/chats.css'); ?>">
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo filemtime('assets/css/index.css'); ?>">
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
                <img src="../uploads/profilePictures/<?php echo $userData['id']; ?>.png" alt="Profiel">
            </div>
        </div>
    </header>

    <div class="love-tester-wrapper">
        <div class="love-tester-container">
            <h2>Love Tester</h2>

            <div class="love-tester-form">
                <input type="text" name="name1" class="name1" placeholder="Jouw naam" value="mitchell" />
                <input type="text" name="name2" class="name2" placeholder="Naam van je Crush" value="mylène" />
                <button name="test_love" class="test_love">Bereken</button>
            </div>

            <div class="heart-container">
                <svg class="heartSvg" viewBox="0 0 100 100">
                    <defs>
                        <clipPath id="heartClip">
                            <rect id="clipRect" x="10" y="90" width="80" height="0"></rect>
                        </clipPath>
                    </defs>

                    <path d="M10,30 A20,20 0 0,1 50,30 A20,20 0 0,1 90,30 Q90,60 50,90 Q10,60 10,30 Z" fill="#FB7F8D" stroke="black" stroke-width="2" />
                    <path id="redFill" d="M10,30 A20,20 0 0,1 50,30 A20,20 0 0,1 90,30 Q90,60 50,90 Q10,60 10,30 Z" fill="red" stroke="black" stroke-width="2" clip-path="url(#heartClip)" />
                    <text x="50" y="50" class="percentageText">0%</text>
                </svg>
            </div>

            <div class="history-container">
                <h3>Geschiedenis</h3>
                <ul class="history-list">
                    <?php
                    foreach($history as $historyItem){
                        ?>
                    <li>
                        <span><strong><?php echo htmlspecialchars($historyItem['name1']); ?></strong> &amp;
                            <strong><?php echo htmlspecialchars($historyItem['name2']); ?></strong> -
                            <?php echo $historyItem['percentage']; ?>%</span>
                        <button class="share-button">Deel</button>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
                <p class="no-history" style="display:<?php echo $historyCount > 0 ? 'none' : 'block' ?>;">Nog geen
                    geschiedenis beschikbaar.</p>
                <button class="clear-history"
                    style="display:<?php echo $historyCount > 0 ? 'block' : 'none' ?>;">Geschiedenis
                    Verwijderen</button>
            </div>
        </div>
    </div>

    <div class="share-menu" id="shareMenu">
        <div class="share-menu-content">
            <h3>Deel je laatste Love Tester-resultaat</h3>
            <p id="shareInfoText"></p>
            <div class="chat-list">
                <div class="chat-item-share">
                    <img class="avatar" alt="Profile" src="../uploads/profilePictures/1.png">
                    <span class="chat-username"><?php echo htmlspecialchars($username); ?></span>
                    <button onclick="shareToChat(<?php echo $anthorUserId; ?>)">
                        Deel
                    </button>
                </div>
            </div>
            <button class="close-menu" onclick="closeShareMenu()">Sluit</button>
        </div>
    </div>

    <div class="share-menu-overlay" id="shareMenuOverlay" onclick="closeShareMenu()"></div>

    <div id="notification-container"></div>
    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js') ?>;
    </script>
    <script src="assets/js/index.js?v=<?php echo filemtime('assets/js/index.js'); ?>"></script>
</body>

</html>