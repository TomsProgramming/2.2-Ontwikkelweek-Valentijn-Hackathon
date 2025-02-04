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
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo filemtime('assets/css/styles.css') ?>">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 id="form-title">Register</h2>
            <form id="auth-form">
                <div class="input-group">
                    <input type="text" id="username" placeholder="Gebruikersnaam" required>
                </div>
                <div class="input-group">
                    <input type="email" id="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Wachtwoord" required>
                </div>
                <div class="input-group">
                    <input type="password" id="passwordCopy" placeholder="Herhaal Wachtwoord" required>
                </div>
                <button type="submit" class="btn">Registreer</button>
                <p class="toggle-text">Al een account? <a href="login.php" id="toggle-form">Login hier</a></p>
            </form>
        </div>
    </div>
    <div class="notification"></div>
    <script src="assets/js/notification.js?v=<?php echo filemtime('assets/js/notification.js') ?>"></script>
    <script src="assets/js/register.js?v=<?php echo filemtime('assets/js/register.js') ?>"></script>
</body>
</html>
