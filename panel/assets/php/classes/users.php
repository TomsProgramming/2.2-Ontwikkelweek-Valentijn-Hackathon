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
}
?>