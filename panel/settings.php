<?php
require '../config.php';

$title = 'Valentijn Hackathon - Instellingen';

if (!$loggedIn) {
    echo '<script>window.location.href = "../login-register/login.php";</script>';
    exit;
}

if ($deviceData['emailVerified'] == 0) {
    echo '<script>window.location.href = "../login-register/verification.php";</script>';
    exit;
}

$notificationsSounds = glob("../sounds/notifications/*.mp3");
$sendMessageSounds = glob("../sounds/sendMessage/*.mp3");
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/settings.css?v=<?php echo filemtime('assets/css/settings.css'); ?>">
</head>

<body>
    <?php include 'includes/navHeader.php'; ?>

    <div class="settings-wrapper">
        <div class="settings-illustration"></div>

        <div class="settings-container">
            <h2 class="settings-title">Instellingen</h2>
            <p class="settings-subtitle">Beheer je account en voorkeuren</p>

            <div class="settings-section">
                <h3>Gebruikersnaam</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="form-group floating-label-group">
                        <input type="text" id="username" name="username" placeholder=" " autocomplete="off">
                        <label for="username">Nieuwe gebruikersnaam</label>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3>Wachtwoord wijzigen</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="form-group floating-label-group">
                        <input type="password" id="current_password" name="current_password" placeholder=" "
                            autocomplete="off">
                        <label for="current_password">Huidig wachtwoord</label>
                    </div>
                    <div class="form-group floating-label-group">
                        <input type="password" id="new_password" name="new_password" placeholder=" " autocomplete="off">
                        <label for="new_password">Nieuw wachtwoord</label>
                    </div>
                    <div class="form-group floating-label-group">
                        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder=" "
                            autocomplete="off">
                        <label for="confirm_new_password">Bevestig nieuw wachtwoord</label>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3>Notificaties</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="toggle-container">
                        <span>Ontvang notificaties</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifications" name="notifications">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3>Notificatiegeluid</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="form-group sound-container">
                        <label for="notification-sound">Kies je meldingsgeluid</label>
                        <div class="sound-selector">
                            <select id="notification-sound" name="notification_sound">
                                <?php
                                if (!empty($notificationsSounds)) {
                                    $first = true;
                                    foreach ($notificationsSounds as $file) {
                                        ?>
                                        <option value="<?php echo pathinfo($file, PATHINFO_FILENAME) ?>" <?php echo $deviceData['notificationSound'] === pathinfo($file, PATHINFO_FILENAME) ? 'selected' : '' ?>><?php echo pathinfo($file, PATHINFO_FILENAME) ?></option>
                                        <?php
                                    }   
                                }
                                ?>
                                <option value="nothing">Geen</option>
                            </select>
                            <button type="button" id="soundTestNotification" class="sound-test-button">🔊 Test</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3>Verzendgeluid</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="form-group sound-container">
                        <label for="send-sound">Kies je verzendgeluid</label>
                        <div class="sound-selector">
                            <select id="send-sound" name="send_sound">
                                <?php
                                if (!empty($sendMessageSounds)) {
                                    $first = true;
                                    foreach ($sendMessageSounds as $file) {
                                        ?>
                                        <option value="<?php echo pathinfo($file, PATHINFO_FILENAME) ?>" <?php echo $deviceData['sendMessageSound'] === pathinfo($file, PATHINFO_FILENAME) ? 'selected' : '' ?>><?php echo pathinfo($file, PATHINFO_FILENAME) ?></option>
                                        <?php
                                    }   
                                }
                                ?>
                                <option value="nothing">Geen</option>
                            </select>
                            <button type="button" id="soundTestSendMessage" class="sound-test-button">🔊 Test</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3>Achtergrondmuziek</h3>
                <form action="#" method="POST" class="settings-form">
                    <div class="toggle-container">
                        <span>Achtergrondmuziek aan</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="background-music" name="background_music" <?php echo $deviceData['backgroundSound'] === 1 ? 'checked' : '' ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </form>
            </div>

            <div class="settings-actions">
                <a href="index.php" class="cancel-button">Annuleren</a>
                <button class="save-button">Opslaan</button>
            </div>
        </div>
    </div>

    <div id="notification-container"></div>
    <?php include 'includes/videoShowcase.php'; ?>
    <script>
    const serviceWorkerVersion = <?php echo filemtime('../service-worker.js') ?>;
    </script>
    <script src="assets/js/settings.js?v=<?php echo filemtime('assets/js/settings.js'); ?>"></script>
</body>

</html>