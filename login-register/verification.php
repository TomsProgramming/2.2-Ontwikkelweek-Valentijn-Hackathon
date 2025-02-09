<?php
require '../config.php';
require 'assets/php/classes/verification.php';
$codeVerzonden = false;

if($loggedIn && $deviceData['emailVerified'] == 1){
    echo '<script>window.location.href = "../index.php";</script>';
}

$verificationSentStatus = verification::hasCodeBeenSent();
if($verificationSentStatus === 1){
    echo '<script> window.location.href = "../index.php"; </script>';
    exit;
}elseif($verificationSentStatus == false){
    verification::createAndSendCode('../');
    $codeVerzonden = true;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentijn Verificatie</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="manifest" href="../manifest.json">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 id="form-title">Verificatie</h2>
            <form id="auth-form">
                <div class="input-group">
                    <input type="text" id="verificationCode" placeholder="Code" autocomplete="off" required>
                </div>
                <button type="submit" class="btn">Verder</button>
                <p class="toggle-text">Geen email ontvangen? <a class="resendVerificationCode" id="toggle-form">Verstuur email opnieuw</a></p>
                <p class="toggle-text"><a href="logout.php" id="toggle-form">Uitloggen</a></p>
            </form>
        </div>
    </div>
    <div class="notification"></div>
    <script src="assets/js/notification.js?v=<?php echo filemtime('assets/js/notification.js') ?>"></script>
    <script src="assets/js/verification.js?v=<?php echo filemtime('assets/js/verification.js') ?>"></script>
</body>
<?php
if($codeVerzonden){
    echo '<script> notification("success", "Er is een verificatiecode naar je email gestuurd."); </script>';
}
?>
</html>
