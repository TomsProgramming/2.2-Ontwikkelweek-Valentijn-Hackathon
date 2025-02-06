<?php
class chats
{   
    static function getChatNotifications($contactId)
    {
        global $conn, $userData;

        $selectUnreadedMessages = $conn->prepare("SELECT id FROM messages WHERE senderId = :senderId AND receiverId = :receiverId AND readed = 0");
        $selectUnreadedMessages->bindParam(':senderId', $contactId);
        $selectUnreadedMessages->bindParam(':receiverId', $userData['id']);
        $selectUnreadedMessages->execute();
        $unreadedMessages = $selectUnreadedMessages->rowCount();

        return $unreadedMessages;
    }

    static function getLastChatMessage($contactId)
    {
        global $conn, $userData, $deviceData;

        $lastMessage = $conn->prepare("SELECT id, message, createdAt FROM messages WHERE (senderId = :senderId AND receiverId = :receiverId) OR (senderId = :receiverId AND receiverId = :senderId) ORDER BY createdAt DESC LIMIT 1");
        $lastMessage->bindParam(':senderId', $contactId);
        $lastMessage->bindParam(':receiverId', $userData['id']);
        $lastMessage->execute();
        $lastMessage = $lastMessage->fetch();

        if(!empty($lastMessage['createdAt'])){
            $utcTime = new DateTime($lastMessage['createdAt'], new DateTimeZone('UTC'));
            $userTime = $utcTime->setTimezone(new DateTimeZone($deviceData['timezone']));
            $currentTime = $userTime->format('Y-m-d H:i:s');
            $lastMessage['createdAt'] = $currentTime;
            return $lastMessage;
        }else{
            return false;
        }
    }

    static function readMessages($contactUsername)
    {
        global $conn, $userData;

        $selectContact = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $selectContact->bindParam(':username', $contactUsername);
        $selectContact->execute();
        if($selectContact->rowCount() > 0){
            $contactData = $selectContact->fetch();
            $contactId = $contactData['id'];

            $readMessages = $conn->prepare("UPDATE messages SET readed = 1 WHERE senderId = :contactId AND receiverId = :userId");
            $readMessages->bindParam(':userId', $userData['id']);
            $readMessages->bindParam(':contactId', $contactId);
            $readMessages->execute();

            return json_encode(array("success" => true));
        }else{
            return json_encode(array("success" => false, "error" => "Gebruiker niet gevonden"));
        }
    }

    static function getMessages($contactUsername)
    {
        global $conn, $userData;

        $selectContact = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $selectContact->bindParam(':username', $contactUsername);
        $selectContact->execute();
        if($selectContact->rowCount() > 0){
            $contactData = $selectContact->fetch();
            $contactId = $contactData['id'];

            $getMessages = $conn->prepare("SELECT * FROM messages WHERE (senderId = :userId AND receiverId = :contactId) OR (senderId = :contactId AND receiverId = :userId) ORDER BY createdAt ASC");
            $getMessages->bindParam(':userId', $userData['id']);
            $getMessages->bindParam(':contactId', $contactId);
            $getMessages->execute();
            $messages = $getMessages->fetchAll();

            foreach($messages as $key => $message){
                if($message['senderId'] == $userData['id']){
                    $messages[$key]['type'] = "sent";
                }else{
                    $messages[$key]['type'] = "received";
                }
            }

            chats::readMessages($contactUsername);

            return json_encode(array("success" => true, "messages" => $messages));
        }else{
            return json_encode(array("success" => false, "error" => "Gebruiker niet gevonden"));
        }
    }
}
?>