<?php
class users
{
    static function addContact($username)
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
            $currentTime = date('Y-m-d H:i:s');

            $addContact = $conn->prepare("INSERT INTO contacts (userId, contactId, createdAt) VALUES (:userId, :contactId, :createdAt)");
            $addContact->bindParam(':userId', $userData['id']);
            $addContact->bindParam(':contactId', $contactData['id']);
            $addContact->bindParam(':createdAt', $currentTime);
            $addContact->execute();

            return json_encode(array("success" => true, "contactId" => $contactData['id']));
        }else{
            return json_encode(array("success" => false, "error" => "Gebruiker niet gevonden"));
        }
    }
}
?>