<?php
require '../../../config.php';

require 'classes/users.php';

$raw_post_data = file_get_contents('php://input');
$post_data = json_decode($raw_post_data, true);

header('Content-Type: application/json');

if($post_data['function']){
    $function = $post_data['function'];
    if($function == "register" && isset($post_data['username']) && isset($post_data['password']) && isset($post_data['passwordCopy'])){
        echo users::register($post_data['username'], $post_data['password'], $post_data['passwordCopy']);
    }elseif ($function == "login" && isset($post_data['username']) && isset($post_data['password'])){
        echo users::login($post_data['username'], $post_data['password']);
    }else{
        echo json_encode(array("success" => false, "error" => "Function does not exist", "function" => $function));
    }
}else{
    echo json_encode(array("success" => false, "error" => "No function provided"));
}
?>