<?php
class users
{
    static function register($username, $password, $passwordCopy)
    {
        global $conn;

        $usernameCheck = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $usernameCheck->bindParam(':username', $username);
        $usernameCheck->execute();

        if($usernameCheck->rowCount() > 0){
            return json_encode(array("success" => false, "error" => "Gebruikersnaam bestaat al"));
        }else{
            if($password == $passwordCopy){
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $registerUser = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $registerUser->bindParam(':username', $username);
                $registerUser->bindParam(':password', $passwordHash);
                $registerUser->execute();

                $lastId = $conn->lastInsertId();
                $token = $lastId . bin2hex(random_bytes(32));

                $updateToken = $conn->prepare("UPDATE users SET token = :token WHERE id = :id");
                $updateToken->bindParam(':token', $token);
                $updateToken->bindParam(':id', $lastId);
                $updateToken->execute();

                setcookie("token", $token, time() + (86400 * 30), "/");
                return json_encode(array("success" => true));
            }else{
                return json_encode(array("success" => false, "error" => "Wachtwoorden komen niet overeen"));
            }
        }
    }

    static function login($username, $password){
        global $conn;

        $userCheck = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $userCheck->bindParam(':username', $username);
        $userCheck->execute();

        if($userCheck->rowCount() > 0){
            $userData = $userCheck->fetch();
            if(password_verify($password, $userData['password'])){
                $token = $userData['id'] . bin2hex(random_bytes(32));

                $updateToken = $conn->prepare("UPDATE users SET token = :token WHERE id = :id");
                $updateToken->bindParam(':token', $token);
                $updateToken->bindParam(':id', $userData['id']);
                $updateToken->execute();

                setcookie("token", $token, time() + (86400 * 30), "/");
                return json_encode(array("success" => true));
            }else{
                return json_encode(array("success" => false, "error" => "U heeft de gebruikersnaam of wachtwoord verkeerd ingevoerd"));
            }
        }else{
            return json_encode(array("success" => false, "error" => "U heeft de gebruikersnaam of wachtwoord verkeerd ingevoerd"));
        }
    }
}
?>