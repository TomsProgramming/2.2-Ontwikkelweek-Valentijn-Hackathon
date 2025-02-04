<?php
class chats
{
    static function getMessages($contactId)
    {
        global $conn, $userData;

        $getMessages = $conn->prepare("SELECT * FROM messages WHERE (senderId = :userId AND receiverId = :contactId) OR (senderId = :contactId AND receiverId = :userId) ORDER BY createdAt ASC");
        $getMessages->bindParam(':userId', $userData['id']);
        $getMessages->bindParam(':contactId', $contactId);
        $getMessages->execute();
        $messages = $getMessages->fetchAll();

        return json_encode(array("success" => true, "messages" => $messages));
    }
}
?>