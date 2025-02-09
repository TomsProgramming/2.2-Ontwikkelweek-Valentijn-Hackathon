<?php
class users
{
    static function validUsernameCheck($username)
    {
        global $conn, $userData;

        if($userData['username'] == $username){
            return json_encode(array("success" => false, "error" => "U kunt uzelf niet toevoegen"));
        }

        $usernameCheck = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $usernameCheck->bindParam(':username', $username);
        $usernameCheck->execute();

        if($usernameCheck->rowCount() > 0){
            $contactData = $usernameCheck->fetch();

            $chatNotifications = chats::getChatNotifications($contactData['id']);
            $lastMessage = chats::getLastChatMessage($contactData['id']);

            return json_encode(array("success" => true, "contactId" => $contactData['id'], "chatNotifications" => $chatNotifications, "lastMessage" => $lastMessage));
        }else{
            return json_encode(array("success" => false, "error" => "Gebruiker niet gevonden"));
        }
    }

    static function updateNotificationData($set, $data)
    {
        global $conn, $deviceData;

        if($set === true){
            $endpoint = $data['endpoint'];
            $p256dh = $data['keys']['p256dh'];
            $auth = $data['keys']['auth'];

            $updateNotification = $conn->prepare("UPDATE devices SET endpoint = :endpoint, p256dh = :p256dh, auth = :auth WHERE id = :deviceId");
            $updateNotification->bindParam(':endpoint', $endpoint);
            $updateNotification->bindParam(':p256dh', $p256dh);
            $updateNotification->bindParam(':auth', $auth);
            $updateNotification->bindParam(':deviceId', $deviceData['id']);
            
            if($updateNotification->execute()){
                return json_encode(array("success" => true));
            }else{
                return json_encode(array("success" => false, "error" => "Er is iets fout gegaan", "phpError" => $updateNotification->errorInfo()));
            }

            return json_encode(array("success" => true));
        }else{
            $updateNotification = $conn->prepare("UPDATE devices SET endpoint = NULL, p256dh = NULL, auth = NULL WHERE id = :deviceId");
            $updateNotification->bindParam(':deviceId', $deviceData['id']);
            $updateNotification->execute();

            return json_encode(array("success" => true));
        }
    }

    static function changeUsername($newUsername)
    {
        global $conn, $redis, $userData;
        $userId = $userData['id'];

        $usernameCheck = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $usernameCheck->bindParam(':username', $newUsername);
        $usernameCheck->execute();

        if($usernameCheck->rowCount() > 0){
            return json_encode(array("success" => false, "error" => "Deze gebruikersnaam is al in gebruik"));
        }

        if (!$redis->exists("valentijnhackathon:change:username:limit:$userId")) {
            $redis->setex("valentijnhackathon:change:username:limit:$userId", 120, 1);
        }else{
            $redis->incr("valentijnhackathon:change:username:limit:$userId");
        }

        if($redis->get("valentijnhackathon:change:username:limit:$userId") > 1){
            return json_encode(array("success" => false, "error" => "U kunt uw gebruikersnaam maximaal één keer per twee minuten wijzigen."));
        }

        $safeUsername = htmlspecialchars($newUsername, ENT_QUOTES, 'UTF-8');

        if(empty($safeUsername)){
            return json_encode(array("success" => false, "error" => "Gebruikersnaam mag niet leeg zijn"));
        }

        if (!preg_match('/^[a-zA-Z0-9._-]{3,20}$/', $safeUsername)) {
            return json_encode(array("success" => false, "error" => "Gebruikersnaam mag alleen bestaan uit letters, cijfers, underscores en streepjes en moet tussen de 3 en 20 tekens lang zijn"));
        }

        $updateUsername = $conn->prepare("UPDATE users SET username = :username WHERE id = :userId");
        $updateUsername->bindParam(':username', $safeUsername);
        $updateUsername->bindParam(':userId', $userData['id']);
        $updateUsername->execute();

        $mailVariables =
        [
            "newUsername" => $safeUsername
        ];

        sendMail("../../../emailTemplates/usernameChange.html", "../../../emailTemplates/usernameChange.txt", $mailVariables, $userData['email'], "Gebruikersnaam gewijzigd");

        return json_encode(array("success" => true));
    }

    static function changePassword($currentPassword, $newPassword, $confirmNewPassword)
    {
        global $conn, $redis, $userData, $deviceData;
        $userId = $userData['id'];

        if($newPassword != $confirmNewPassword){
            return json_encode(array("success" => false, "error" => "De wachtwoorden komen niet overeen"));
        }

        if(password_verify($currentPassword, $userData['password'])){
            if (!$redis->exists("valentijnhackathon:change:password:limit:$userId")) {
                $redis->setex("valentijnhackathon:change:password:limit:$userId", 120, 1);
            }else{
                $redis->incr("valentijnhackathon:change:password:limit:$userId");
            }
    
            if($redis->get("valentijnhackathon:change:password:limit:$userId") > 1){
                return json_encode(array("success" => false, "error" => "U kunt uw gebruikersnaam maximaal één keer per twee minuten wijzigen."));
            }

            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updatePassword = $conn->prepare("UPDATE users SET password = :password WHERE id = :userId");
            $updatePassword->bindParam(':password', $newPassword);
            $updatePassword->bindParam(':userId', $userData['id']);
            $updatePassword->execute();

            $mailVariables = [];

            sendMail("../../../emailTemplates/passwordChange.html", "../../../emailTemplates/passwordChange.txt", $mailVariables, $userData['email'], "Wachtwoord gewijzigd");

            $deleteAllDevices = $conn->prepare("DELETE FROM devices WHERE userId = :userId");
            $deleteAllDevices->bindParam(':userId', $userData['id']);
            $deleteAllDevices->execute();

            $userData = [];
            $deviceData = [];
            setcookie("token", "", time() - 3600, "/");

            return json_encode(array("success" => true));
        }else{
            return json_encode(array("success" => false, "error" => "Het huidige wachtwoord is onjuist"));
        }
    }

    static function getSoundsConfig()
    {
        global $deviceData;

        return json_encode(array("success" => true, "sounds" => ["notificationSound" => $deviceData['notificationSound'], "sendMessageSound" => $deviceData['sendMessageSound'], "backgroundSound" => $deviceData['backgroundSound']]));
    }

    static function changeSounds($notificationSound, $sendMessageSound, $backgroundSound)
    {
        global $conn, $redis, $userData, $deviceData;

        if(!file_exists("../../../sounds/notifications/$notificationSound.mp3") && $notificationSound != "nothing"){
            return json_encode(array("success" => false, "error" => "Notificatie geluid niet gevonden"));
        }

        if(!file_exists("../../../sounds/sendMessage/$sendMessageSound.mp3") && $sendMessageSound != "nothing"){
            return json_encode(array("success" => false, "error" => "Verzend geluid niet gevonden"));
        }

        $userId = $userData['id'];
        $backgroundSound = $backgroundSound == true ? 1 : 0;

        if (!$redis->exists("valentijnhackathon:change:sounds:limit:$userId")) {
            $redis->setex("valentijnhackathon:change:sounds:limit:$userId", 5, 1);
        }else{
            $redis->incr("valentijnhackathon:change:sounds:limit:$userId");
        }

        if($redis->get("valentijnhackathon:change:sounds:limit:$userId") > 1){
            return json_encode(array("success" => false, "error" => "U kunt uw geluiden maximaal één keer per 5 seconden wijzigen."));
        }

        $updateSounds = $conn->prepare("UPDATE devices SET notificationSound = :notificationSound, sendMessageSound = :sendMessageSound, backgroundSound = :backgroundSound WHERE id = :deviceId");
        $updateSounds->bindParam(':notificationSound', $notificationSound);
        $updateSounds->bindParam(':sendMessageSound', $sendMessageSound);
        $updateSounds->bindParam(':backgroundSound', $backgroundSound);
        $updateSounds->bindParam(':deviceId', $deviceData['id']);
        $updateSounds->execute();

        return json_encode(array("success" => true));
    }
}
?>