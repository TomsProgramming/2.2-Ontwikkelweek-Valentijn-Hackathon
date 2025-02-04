<?php
require '../../../config.php';

require 'classes/users.php';
require 'classes/verification.php';

header('Content-Type: application/json');

$raw_post_data = file_get_contents('php://input');
$post_data = json_decode($raw_post_data, true);

if($post_data['function']){
    $function = $post_data['function'];

    if($function == "register" && isset($post_data['username']) && isset($post_data['email']) && isset($post_data['password']) && isset($post_data['passwordCopy']) && isset($post_data['timezone'])){
        if(!$redis->exists("valentijnhackathon:register:limit:$ipAdress") || $redis->get("valentijnhackathon:register:limit:$ipAdress") < 5){
            if (!$redis->exists("valentijnhackathon:register:limit:$ipAdress")) {
                $redis->setex("valentijnhackathon:register:limit:$ipAdress", 120, 1);
            }else{
                $redis->incr("valentijnhackathon:register:limit:$ipAdress");
            }

            echo users::register($post_data['username'], $post_data['email'], $post_data['password'], $post_data['passwordCopy'], $post_data['timezone']);
        }else{
            echo json_encode(array("success" => false, "error" => "Er zijn te veel registratiepogingen gedaan vanaf dit IP-adres. Probeer het over enkele minuten opnieuw."));
        }
    }elseif ($function == "login" && isset($post_data['username']) && isset($post_data['password']) && isset($post_data['timezone'])){
        echo users::login($post_data['username'], $post_data['password'], $post_data['timezone']);
    }elseif ($function == "resendVerificationCode"){
        echo verification::resendCode('../../../');
    }elseif($function == "verifyCode" && isset($post_data['code'])){
        echo verification::verifyCode('../../../', $post_data['code']);
    }elseif($function == 'logout'){
        echo users::logout();
    }else{
        echo json_encode(array("success" => false, "error" => "Function does not exist", "function" => $function));
    }
}else{
    echo json_encode(array("success" => false, "error" => "No function provided"));
}
?>