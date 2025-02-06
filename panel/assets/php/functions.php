<?php
require '../../../config.php';
require 'classes/users.php';
require 'classes/chats.php';
require 'classes/loveTester.php';

header('Content-Type: application/json');

if($loggedIn && $deviceData['emailVerified'] == 1){
    $raw_post_data = file_get_contents('php://input');
    $post_data = json_decode($raw_post_data, true);

    if($post_data['function']){
        $function = $post_data['function'];
        if($function == "validUsernameCheck" && isset($post_data['username'])){
           echo users::validUsernameCheck($post_data['username']);
        }elseif($function == "getMessages" && isset($post_data['contactUsername'])){
            echo chats::getMessages($post_data['contactUsername']);
        }elseif($function == "readMessages" && isset($post_data['contactUsername'])){
            echo chats::readMessages($post_data['contactUsername']);
        }elseif($function == "updateNotificationData" && isset($post_data['setNotification']) && isset($post_data['subscription'])){
            echo users::updateNotificationData($post_data['setNotification'], $post_data['subscription']);
        }elseif($function == "testLove" && isset($post_data['name1']) && isset($post_data['name2'])){
            echo loveTester::test($post_data['name1'], $post_data['name2']);
        }elseif($function == "clearLoveTesterHistory"){
            echo loveTester::clearHistory();
        }
    }else{
        echo json_encode(array("success" => false, "error" => "Geen functie opgegeven"));
    }
}else{
    echo json_encode(array("success" => false, "error" => "U bent niet ingelogd"));
}
?>