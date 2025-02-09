<?php
require '../config.php';

if($loggedIn){
    $deviceId = $deviceData['id'];
    
    $deleteDevice = $conn->prepare("DELETE FROM devices WHERE id = :id");
    $deleteDevice->bindParam(':id', $deviceId);
    $deleteDevice->execute();
}

setcookie('token', '', time() - 3600, '/');
echo '<script>window.location.href = "../";</script>';
?>