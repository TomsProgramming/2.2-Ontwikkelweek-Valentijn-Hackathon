<?php
require '../config.php';

if(!$loggedIn){
    echo '<script>window.location.href = "../login-register/login.php";</script>';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Interface</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <script>
    function toggleAddUserMenu() {
      const menu = document.getElementById("addUserMenu");
      menu.style.display = menu.style.display === "block" ? "none" : "block";
    }
  </script>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="chat-list">
        <div class="chat-item">Contact 1</div>
        <div class="chat-item">Contact 2</div>
        <div class="chat-item">Contact 3</div>
      </div>
      <div class="sidebar-buttons">
        <div class="add-user" onclick="toggleAddUserMenu()">+ Voeg contact toe</div>
        <div class="logout">🔒 Uitloggen</div>
      </div>
    </div>
    <div class="chat-container">
      <!-- Container voor alle berichten -->
      <div class="messages">
        <div class="message bot">
          <div class="avatar"></div>
          <div class="bubble">Hoi, hoe gaat het?</div>
        </div>
        <div class="message user">
          <div class="bubble">Goed, met jou?</div>
          <div class="avatar"></div>
        </div>
      </div>
      <!-- Input-container blijft altijd onderaan staan -->
      <div class="input-container">
        <input type="text" placeholder="Typ een bericht...">
        <button>Verzenden</button>
      </div>
    </div>
  </div>
  <div id="addUserMenu" class="add-user-menu">
    <h3>Nieuwe contact toevoegen</h3>
    <input type="text" placeholder="Gebruikersnaam">
    <button onclick="toggleAddUserMenu()">Toevoegen</button>
  </div>
</body>
</html>
