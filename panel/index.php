<?php
require '../config.php';

$title = 'Valentijn Hackathon - Love Tester';

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
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo filemtime('assets/css/index.css'); ?>">
</head>

<body>    
    <?php include 'includes/navHeader.php'; ?>
    <div class="love-tester-wrapper">
        <div class="love-tester-container">
            <h2>Love Tester</h2>

            <div class="love-tester-form">
                <input type="text" name="name1" class="name1" placeholder="Jouw naam" />
                <input type="text" name="name2" class="name2" placeholder="Naam van je Crush" />
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
                        <span><strong><?php echo htmlspecialchars($historyItem['name1']); ?></strong> &amp; <strong><?php echo htmlspecialchars($historyItem['name2']); ?></strong> - <?php echo $historyItem['percentage']; ?>%</span>
                        <button class="share-button" onclick="loveTesterShare.shareMenu(<?php echo $historyItem['id'] ?>)">Deel</button>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
                <p class="no-history" style="display:<?php echo $historyCount > 0 ? 'none' : 'block' ?>;">Nog geen geschiedenis beschikbaar.</p>
                <button class="clear-history" style="display:<?php echo $historyCount > 0 ? 'block' : 'none' ?>;">Geschiedenis Verwijderen</button>
            </div>
        </div>
    </div>

    <div class="share-menu" id="shareMenu">
        <div class="share-menu-content">
            <h3>Deel je Love Tester-resultaat</h3>
            <p id="shareInfoText"></p>
            <div class="chat-list"></div>
            <button class="close-menu">Sluit</button>
        </div>
    </div>

    <div class="share-menu-overlay" id="shareMenuOverlay"></div>

    <div id="notification-container"></div>

    <?php include 'includes/videoShowcase.php'; ?>

    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js') ?>;
    </script>
    <script src="assets/js/index.js?v=<?php echo filemtime('assets/js/index.js'); ?>"></script>
</body>

</html>