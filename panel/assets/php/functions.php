<?php
require '../../../config.php';
require 'classes/users.php';
require 'classes/chats.php';

header('Content-Type: application/json');

if($loggedIn && $deviceData['emailVerified'] == 1){
    $raw_post_data = file_get_contents('php://input');
    $post_data = json_decode($raw_post_data, true);

    if($post_data['function']){
        $function = $post_data['function'];
        if($function == "addContact" && isset($post_data['username'])){
           echo users::addContact($post_data['username']);
        }elseif($function == "getMessages" && isset($post_data['contactId'])){
            echo chats::getMessages($post_data['contactId']);
        }
    }else{
        echo json_encode(array("success" => false, "error" => "Geen functie opgegeven"));
    }
}else{
    echo json_encode(array("success" => false, "error" => "U bent niet ingelogd"));
}
?>