<?php
require '../config.php';

if(!$loggedIn){
    echo '<script>window.location.href = "../login-register/login.php";</script>';
}

if($deviceData['emailVerified'] == 0){
    echo '<script>window.location.href = "../login-register/verification.php";</script>';
}

$contactsList = $conn->prepare("SELECT * FROM contacts WHERE userId = :userId");
$contactsList->bindParam(':userId', $userData['id']);
$contactsList->execute();
$contacts = $contactsList->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Valentijn Hackathon</title>
  <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo filemtime('assets/css/styles.css') ?>">
</head>
<body>
  <div class="container">

    <div class="sidebar">
      <div class="header">
        <h1>Valentijn Hackathon</h1>
      </div>
      <div class="chat-list">
        <?php
        foreach($contacts as $contact){
            $contactData = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $contactData->bindParam(':id', $contact['contactId']);
            $contactData->execute();
            $contactData = $contactData->fetch();
            echo '<div class="chat-item" onclick="contacts.onClickContact(this)" data-id="'.$contactData['id'].'" data-username="'.$contactData['username'].'">'.$contactData['username'].'</div>';
        }
        ?>
      </div>
      <div class="sidebar-buttons">
      <button class="profile">
          <img src="../uploads/profilePictures/<?php echo $userData['id'] ?>.png" alt="Profiel" class="profile-avatar">
        </button>
        <button class="add-user">+ Voeg contact toe</button>
      </div>
    </div>

    <div class="chat-container">
      <div class="messages">
        <div class="message receiver">
          <div class="bubble">Hoi, hoe gaat het?</div>
        </div>
        <div class="message sender">
          <div class="bubble">Goed, met jou?</div>
        </div>
      </div>
      <div class="input-container">
        <input class="message" type="text" placeholder="Typ een bericht...">
        <button class="send">Verzenden</button>
      </div>
    </div>
  </div>

  <div id="addUserMenu" class="add-user-menu">
    <h3>Nieuwe contact toevoegen</h3>
    <input type="text" class="username" placeholder="Gebruikersnaam">
    <button class="close">Sluit</button>
    <button class="add-user">Toevoegen</button>
  </div>

  <div id="notification-container"></div>
</body>
<script src="assets/js/main.js?v=<?php echo filemtime('assets/js/main.js') ?>"></script>
</html>


