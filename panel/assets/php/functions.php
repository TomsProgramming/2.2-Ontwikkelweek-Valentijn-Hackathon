<?php
require '../../../config.php';
require 'classes/users.php';
require 'classes/chats.php';
require 'classes/loveTester.php';

header('Content-Type: application/json');

if($loggedIn && $deviceData['emailVerified'] == 1){
    $raw_post_data = file_get_contents('php://input');
    $post_data = json_decode($raw_post_data, true);

    if(isset($post_data['function'])){
        $function = $post_data['function'];
        if($function == "validUsernameCheck" && isset($post_data['username'])){
           echo users::validUsernameCheck($post_data['username']);
        }elseif($function == "getMessages" && isset($post_data['contactUsername'])){
            echo chats::getMessages($post_data['contactUsername']);
        }elseif($function == "readMessages" && isset($post_data['contactUsername'])){
            echo chats::readMessages($post_data['contactUsername']);
        }elseif($function == "getChats"){
            echo chats::getChats();
        }elseif($function == "updateNotificationData" && isset($post_data['setNotification']) && isset($post_data['subscription'])){
            echo users::updateNotificationData($post_data['setNotification'], $post_data['subscription']);
        }elseif($function == "testLove" && isset($post_data['name1']) && isset($post_data['name2'])){
            echo loveTester::test($post_data['name1'], $post_data['name2']);
        }elseif($function == "clearLoveTesterHistory"){
            echo loveTester::clearHistory();
        }elseif($function == "changeUsername" && isset($post_data['username'])){
            echo users::changeUsername($post_data['username']);
        }elseif($function == "changePassword" && isset($post_data['currentPassword']) && isset($post_data['newPassword']) && isset($post_data['confirmPassword'])){
            echo users::changePassword($post_data['currentPassword'], $post_data['newPassword'], $post_data['confirmPassword']);
        }elseif($function == "getSoundsConfig"){
            echo users::getSoundsConfig();  
        }elseif($function == "changeSound" && isset($post_data['notificationSound']) && isset($post_data['sendSound']) && isset($post_data['backgroundSound'])){
            echo users::changeSounds($post_data['notificationSound'], $post_data['sendSound'], $post_data['backgroundSound']);
        }
    }else{
        echo json_encode(array("success" => false, "error" => "Geen functie opgegeven"));
    }
}else{
    echo json_encode(array("success" => false, "error" => "U bent niet ingelogd"));
}
?>