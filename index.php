<?php
require 'config.php';

if($loggedIn){
    echo '<script>window.location.href = "panel/";</script>';
}else{
    echo '<script>window.location.href = "login-register/login.php";</script>';
}
?>