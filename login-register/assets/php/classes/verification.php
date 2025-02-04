<?php

class verification
{
    static function hasCodeBeenSent()
    {
        global $conn, $userData, $deviceData;
        $selectDeviceVerification = $conn->prepare("SELECT verificationExpiresAt FROM devices WHERE id = :deviceId");
        $selectDeviceVerification->bindParam(':deviceId', $deviceData['id'], PDO::PARAM_INT);
        $selectDeviceVerification->execute();

        if($selectDeviceVerification->rowCount() == 1){
            $deviceVerification = $selectDeviceVerification->fetch(PDO::FETCH_ASSOC);
            $deviceVerificationExpiresDateTime = ($deviceVerification['verificationExpiresAt'] != null) ? new DateTime($deviceVerification['verificationExpiresAt']) : new DateTime('now');
            $now = new DateTime('now');

            if($deviceVerificationExpiresDateTime > $now){
                return true;
            }else{
                return false;
            }
        }else{
            return 1;
        }
    }

    static function createAndSendCode($mainPath){
        global $conn, $redis, $verificationValidityDuration, $deviceData, $userData;
        $userId = $deviceData['userId'];

        if (!$redis->exists("valentijnhackathon:verification:$userId")) {
            $redis->setex("valentijnhackathon:verification:$userId", 60, 1);
        }else{
            $redis->incr("valentijnhackathon:verification:$userId");
        }

        $code = rand(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+'.$verificationValidityDuration.' seconds'));
        $currentTime = date('Y-m-d H:i:s');

        $updateDevice = $conn->prepare("UPDATE devices SET verificationCode = :code, verificationExpiresAt = :expiresAt, lastUpdatedAt = :lastUpdatedAt WHERE id = :deviceId");
        $updateDevice->bindParam(':code', $code, PDO::PARAM_INT);
        $updateDevice->bindParam(':expiresAt', $expiresAt, PDO::PARAM_STR);
        $updateDevice->bindParam(':lastUpdatedAt', $currentTime, PDO::PARAM_STR);
        $updateDevice->bindParam(':deviceId', $deviceData['id'], PDO::PARAM_INT);
        $updateDevice->execute();

        $utcTime = new DateTime($expiresAt, new DateTimeZone('UTC'));
        $userTime = $utcTime->setTimezone(new DateTimeZone($deviceData['timezone']));
        $expiresAtUserTime = $userTime->format('Y-m-d H:i:s');

        $variables = array(
            "code" => $code,
            "expireDate" => $expiresAtUserTime
        );

        sendMail(''.$mainPath.'emailTemplates/verificationCode.html', ''.$mainPath.'emailTemplates/verificationCode.txt', $variables, $userData['email'], '2-staps verificatie code');
    }

    static function resendCode($mainPath){
        global $conn, $redis, $verificationValidityDuration, $deviceData, $userData;

        $userId = $deviceData['userId'];

        if (!$redis->exists("valentijnhackathon:verification:$userId")) {
            $redis->setex("valentijnhackathon:verification:$userId", 60, 1);
        }else{
            $redis->incr("valentijnhackathon:verification:$userId");
        }

        if($redis->get("valentijnhackathon:verification:$userId") <= 1){
            $verificationSentStatus = verification::hasCodeBeenSent();
            if($verificationSentStatus === 1){
                return false;
            }else if ($verificationSentStatus == false){
                verification::createAndSendCode($mainPath);
            }else{
                $selectDeviceVerification = $conn->prepare("SELECT verificationCode, verificationExpiresAt FROM devices WHERE id = :deviceId");
                $selectDeviceVerification->bindParam(':deviceId', $deviceData['id'], PDO::PARAM_INT);
                $selectDeviceVerification->execute();
                
                if($selectDeviceVerification->rowCount() == 1){
                    $deviceVerification = $selectDeviceVerification->fetch(PDO::FETCH_ASSOC);
                    $code = $deviceVerification['verificationCode'];
                    $expiresAt = $deviceVerification['verificationExpiresAt'];

                    $utcTime = new DateTime($expiresAt, new DateTimeZone('UTC'));
                    $userTime = $utcTime->setTimezone(new DateTimeZone($deviceData['timezone']));
                    $expiresAtUserTime = $userTime->format('Y-m-d H:i:s');

                    $variables = array(
                        "code" => $code,
                        "expireDate" => $expiresAtUserTime
                    );
            
                    sendMail(''.$mainPath.'emailTemplates/verificationCode.html', ''.$mainPath.'emailTemplates/verificationCode.txt', $variables, $userData['email'], '2-staps verificatie code');
                }
            }
        }else{
            return json_encode(array("success" => false, "error" => "Wacht minimaal één minuut voordat u de e-mail opnieuw probeert te versturen."));
        }
    }

    static function verifyCode($mainPath, $code){
        global $conn, $redis, $ipAdress, $deviceData, $userData;
        $userId = $deviceData['userId'];

        if (!$redis->exists("valentijnhackathon:verification:verifyCode:$userId")) {
            $redis->setex("valentijnhackathon:verification:verifyCode:$userId", 60, 1);
        }else{
            $redis->incr("valentijnhackathon:verification:verifyCode:$userId");
        }

        if($redis->get("valentijnhackathon:verification:verifyCode:$userId") <= 3){
            $selectDeviceVerification = $conn->prepare("SELECT verificationCode, verificationExpiresAt FROM devices WHERE id = :deviceId");
            $selectDeviceVerification->bindParam(':deviceId', $deviceData['id'], PDO::PARAM_INT);
            $selectDeviceVerification->execute();

            if($selectDeviceVerification->rowCount() == 1){
                $deviceVerification = $selectDeviceVerification->fetch(PDO::FETCH_ASSOC);
                $verificationCode = $deviceVerification['verificationCode'];
                $verificationExpiresAt = new DateTime($deviceVerification['verificationExpiresAt']);
                $now = new DateTime('now');

                if($verificationCode == $code){
                    if ($verificationExpiresAt > $now) {
                        $currentTime = date('Y-m-d H:i:s');
                        $updateDevice = $conn->prepare("UPDATE devices SET emailVerified = 1, lastUpdatedAt = :lastUpdatedAt WHERE id = :deviceId");
                        $updateDevice->bindParam(':lastUpdatedAt', $currentTime, PDO::PARAM_STR);
                        $updateDevice->bindParam(':deviceId', $deviceData['id'], PDO::PARAM_INT);
                        $updateDevice->execute();

                        $utcTime = new DateTime('now', new DateTimeZone('UTC'));
                        $userTime = $utcTime->setTimezone(new DateTimeZone($deviceData['timezone']));
                        $currentTime = $userTime->format('Y-m-d H:i:s');

                        $apiUrl = "http://ipinfo.io/{$ipAdress}/json?token=98a4b42be4185c";
                        $response = file_get_contents($apiUrl);
                        $data = json_decode($response, true);

                        if ($data && isset($data['city']) && isset($data['region']) && isset($data['country'])) {
                            $location = $data['city'] . ', ' . $data['region'] . ', ' . $data['country'];
                        } else {
                            $location = 'Onbekend';
                        }
                        
                        $variables = array(
                            "loginDate" => $currentTime,
                            "ipAddress" => $ipAdress,
                            "location" => $location
                        );

                        sendMail(''.$mainPath.'emailTemplates/newLogin.html', ''.$mainPath.'emailTemplates/newLogin.txt', $variables, $userData['email'], 'Bevestiging van nieuwe login');
                        return json_encode(array("success" => true));
                    }else{
                        return json_encode(array("success" => false, "error" => "De code is verlopen", "expiresAt" => $verificationExpiresAt, "now" => $now));
                    }
                }else{
                    return json_encode(array("success" => false, "error" => "De code is onjuist"));
                }
            }else{
                return json_encode(array("success" => false, "error" => "Er is geen verificatiecode gevonden."));
            }
        }else{
            return json_encode(array("success" => false, "error" => "U heeft te vaak geprobeerd de code te verifiëren. Wacht minimaal één minuut voordat u het opnieuw probeert."));
        }
    }
} 
?>