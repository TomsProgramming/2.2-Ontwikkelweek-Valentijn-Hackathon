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

// Als de sessie-array voor geschiedenis nog niet bestaat, maken we die aan
if(!isset($_SESSION['love_history'])) {
    $_SESSION['love_history'] = [];
}

// Controleren of het formulier is verstuurd
if(isset($_POST['test_love'])) {
    $name1 = trim($_POST['name1']);
    $name2 = trim($_POST['name2']);

    // Eenvoudige validatie
    if(!empty($name1) && !empty($name2)) {
        // Voorbeeldberekening: random percentage
        $percentage = rand(0, 100);

        // Opslaan in geschiedenis
        $newResult = [
            'name1' => $name1,
            'name2' => $name2,
            'percentage' => $percentage,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        array_unshift($_SESSION['love_history'], $newResult); // Vooraan toevoegen
    }
}

// Geschiedenis verwijderen
if(isset($_POST['clear_history'])) {
    $_SESSION['love_history'] = [];
    echo '<script>window.location.href="index.php";</script>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Valentijn Hackathon - Love Tester</title>
    <!-- Styles -->
    <!-- Je kunt ervoor kiezen om ook chats.css te includen, mocht je dezelfde stijlen willen gebruiken -->
    <link rel="stylesheet" href="assets/css/chats.css?v=<?php echo filemtime('assets/css/chats.css'); ?>">
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo filemtime('assets/css/index.css'); ?>">
    <link rel="manifest" href="../manifest.json">
</head>
<body>
    <!-- Navigatiebalk -->
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

    <!-- Container voor de Love Tester -->
    <div class="love-tester-wrapper">
        <div class="love-tester-container">
            <h2>Love Tester</h2>

            <form method="POST" action="" class="love-tester-form">
                <input type="text" name="name1" placeholder="Jouw naam" required />
                <input type="text" name="name2" placeholder="Naam van je Crush" required />
                <button type="submit" name="test_love">Bereken</button>
            </form>

            <?php
            // Check of er recent een resultaat is
            $laatsteResultaat = null;
            if(!empty($_SESSION['love_history'])) {
                $laatsteResultaat = $_SESSION['love_history'][0];
            }
            ?>

            <!-- Hart + Percentage -->
            <div class="heart-container">
                <?php if($laatsteResultaat): ?>
                    <?php
                    $percentage = $laatsteResultaat['percentage'];
                    $filledHeight = $percentage; // Vulhoogte in procent
                    ?>
                    <div class="heart">
                        <div class="heart-fill" style="height: <?php echo $filledHeight; ?>%;"></div>
                        <div class="heart-percentage"><?php echo $percentage; ?>%</div>
                    </div>
                    <div class="result-info">
                        <p>
                            Resultaat voor 
                            <strong><?php echo htmlspecialchars($laatsteResultaat['name1']); ?></strong> 
                            &amp; 
                            <strong><?php echo htmlspecialchars($laatsteResultaat['name2']); ?></strong>:
                            <strong><?php echo $percentage; ?>%</strong>
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Placeholder voor als er nog geen resultaten zijn -->
                    <div class="heart heart-empty">
                        <div class="heart-percentage">--%</div>
                    </div>
                    <p class="no-result">Vul bovenaan twee namen in om een match te berekenen!</p>
                <?php endif; ?>
            </div>

            <!-- Geschiedenis -->
            <div class="history-container">
                <h3>Geschiedenis</h3>
                <?php if(!empty($_SESSION['love_history'])): ?>
                    <ul class="history-list">
                        <?php foreach($_SESSION['love_history'] as $index => $item): ?>
                            <li>
                                <span>
                                    <strong><?php echo htmlspecialchars($item['name1']); ?></strong> &amp;
                                    <strong><?php echo htmlspecialchars($item['name2']); ?></strong> 
                                    - <?php echo $item['percentage']; ?>%
                                </span>
                                <!-- Deel-knop -->
                                <button type="button"
                                        class="share-button"
                                        onclick="shareResult('<?php echo addslashes($item['name1']); ?>','<?php echo addslashes($item['name2']); ?>', '<?php echo $item['percentage']; ?>')">
                                    Deel
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-history">Nog geen geschiedenis beschikbaar.</p>
                <?php endif; ?>

                <!-- Geschiedenis verwijderen -->
                <?php if(!empty($_SESSION['love_history'])): ?>
                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je de geschiedenis wilt wissen?');">
                        <button type="submit" name="clear_history" class="clear-history">Geschiedenis Verwijderen</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notificatie-container (optioneel, voor evt. meldingen) -->
    <div id="notification-container"></div>

    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js'); ?>;

    // Navigatie (mobiel) toggle
    const menuToggleBtn = document.querySelector('.menu-toggle');
    const navLinks = document.getElementById('navLinks');
    menuToggleBtn.addEventListener('click', () => {
        navLinks.classList.toggle('show');
    });

    // Web Share API (voor mobiel); fallback = alert
    function shareResult(name1, name2, percentage) {
        const shareData = {
            title: "Love Tester Resultaat",
            text: `${name1} & ${name2} scoren ${percentage}% in de Love Tester!`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData)
            .then(() => console.log("Gedeeld via Web Share API"))
            .catch(console.error);
        } else {
            alert(`Je kunt dit resultaat delen:\n\n${shareData.text}\n\nLink: ${shareData.url}`);
        }
    }
    </script>
    <script src="assets/js/index.js?v=<?php echo filemtime('assets/js/index.js'); ?>"></script>
</body>
</html>
