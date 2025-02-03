<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$loggedIn = false;
$userData = [];

try {
    $conn = new PDO("mysql:host=localhost;dbname=valentijnhackathon", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}   

if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];

    $userCheck = $conn->prepare("SELECT * FROM users WHERE token = :token");
    $userCheck->bindParam(':token', $token);
    $userCheck->execute();
    if($userCheck->rowCount() > 0){
        $userData = $userCheck->fetch();
        $loggedIn = true;
    }
}
?>