<?php
require 'config.php';

if($loggedIn){
    if($deviceData['emailVerified'] == 1){
        echo '<script>window.location.href = "panel/index.php";</script>';
    }else{
        echo '<script>window.location.href = "login-register/verification.php";</script>';
    }
}else{
    echo '<script>window.location.href = "login-register/login.php";</script>';
}
?>