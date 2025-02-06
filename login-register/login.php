<?php
require '../config.php';

if($loggedIn){
    echo '<script>window.location.href = "../index.php";</script>';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentijn Login & Register</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="manifest" href="../manifest.json">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 id="form-title">Login</h2>
            <form id="auth-form">
                <div class="input-group">
                    <input type="text" id="username" placeholder="Gebruikersnaam of Email" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Wachtwoord" required>
                </div>
                <button type="submit" class="btn">Login</button>
                <p class="toggle-text">Nog geen account? <a href="register.php" id="toggle-form">Registreer hier</a></p>
            </form>
        </div>
    </div>
    <div class="notification"></div>
    <script src="assets/js/notification.js?v=<?php echo filemtime('assets/js/notification.js') ?>"></script>
    <script src="assets/js/login.js?v=<?php echo filemtime('assets/js/login.js') ?>"></script>
</body>
</html>
