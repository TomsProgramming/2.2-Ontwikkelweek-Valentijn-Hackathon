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
        <div class="profile-menu-container">
            <div class="profile-icon">
                <img src="../uploads/profilePictures/<?php echo $userData['id']; ?>.png" alt="Profiel">
            </div>
            <ul class="profile-dropdown" id="profileDropdown">
                <li><a href="settings.php">Instellingen</a></li>
                <li><a href="../login-register/logout.php">Uitloggen</a></li>
            </ul>
        </div>
    </div>
</header>