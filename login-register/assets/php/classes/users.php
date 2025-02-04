<?php
use YoHang88\LetterAvatar\LetterAvatar;

class users
{
    static function createDevice($userId, $timezone)
    {
        global $conn;

        $currentTime = date('Y-m-d H:i:s');

        $createDevice = $conn->prepare("INSERT INTO devices (userId, timezone, createdAt, lastUpdatedAt) VALUES (:userId, :timezone, :createdAt, :lastUpdatedAt)");
        $createDevice->bindParam(':userId', $userId);
        $createDevice->bindParam(':timezone', $timezone);
        $createDevice->bindParam(':createdAt', $currentTime);
        $createDevice->bindParam(':lastUpdatedAt', $currentTime);
        $createDevice->execute();

        $deviceId = $conn->lastInsertId();

        $token = '';

        while (true)
        {
            $token = $userId . $deviceId . bin2hex(random_bytes(32));
            
            $tokenCheck = $conn->prepare("SELECT * FROM devices WHERE token = :token");
            $tokenCheck->bindParam(':token', $token);
            $tokenCheck->execute();

            if($tokenCheck->rowCount() == 0){
                $currentTime = date('Y-m-d H:i:s');
                $updateToken = $conn->prepare("UPDATE devices SET token = :token, lastUpdatedAt = :lastUpdatedAt WHERE id = :id");
                $updateToken->bindParam(':token', $token);
                $updateToken->bindParam(':lastUpdatedAt', $currentTime);
                $updateToken->bindParam(':id', $deviceId);
                $updateToken->execute();
                break;
            }
        }
        
        setcookie("token", $token, time() + (86400 * 30), "/");
    }

    static function register($username, $email, $password, $passwordCopy, $timezone)
    {
        global $conn;

        $usernameCheck = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $usernameCheck->bindParam(':username', $username);
        $usernameCheck->execute();

        if($usernameCheck->rowCount() > 0){
            return json_encode(array("success" => false, "error" => "Gebruikersnaam bestaat al"));
        }

        $emailCheck = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $emailCheck->bindParam(':email', $email);
        $emailCheck->execute();

        if($emailCheck->rowCount() > 0){
            return json_encode(array("success" => false, "error" => "Email bestaat al"));
        }

        if($password == $passwordCopy){
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $registerUser = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $registerUser->bindParam(':username', $username);
            $registerUser->bindParam(':email', $email);
            $registerUser->bindParam(':password', $passwordHash);
            $registerUser->execute();

            $lastId = $conn->lastInsertId();
            $avatar = new LetterAvatar($username);
            $save = $avatar->saveAs("../../../uploads/profilePictures/$lastId.png");
            self::createDevice($lastId, $timezone);
            return json_encode(array("success" => true));
        }else{
            return json_encode(array("success" => false, "error" => "Wachtwoorden komen niet overeen"));
        }
    }

    static function login($username, $password, $timezone){
        global $conn;

        $userCheck = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
        $userCheck->bindParam(':username', $username);
        $userCheck->execute();

        if($userCheck->rowCount() > 0){
            $userData = $userCheck->fetch();
            if(password_verify($password, $userData['password'])){
                self::createDevice($userData['id'], $timezone);
                return json_encode(array("success" => true));
            }else{
                return json_encode(array("success" => false, "error" => "U heeft de gebruikersnaam of wachtwoord verkeerd ingevoerd"));
            }
        }else{
            return json_encode(array("success" => false, "error" => "U heeft de gebruikersnaam of wachtwoord verkeerd ingevoerd"));
        }
    }

    static function logout(){
        setcookie("token", "", time() - 3600, "/");
        echo json_encode(array("success" => true));
    }
}
?>