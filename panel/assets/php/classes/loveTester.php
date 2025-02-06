<?php

class loveTester
{
    public static function test($name1, $name2)
    {
        global $conn, $userData;

        $name1 = strtolower($name1);
        $name2 = strtolower($name2);

        $selectLoveTesterCombinations = $conn->prepare("SELECT * FROM loveTesterCombinations WHERE (name1 = :name1 AND name2 = :name2) OR (name1 = :name2 AND name2 = :name1)");
        $selectLoveTesterCombinations->bindParam(':name1', $name1);
        $selectLoveTesterCombinations->bindParam(':name2', $name2);
        $selectLoveTesterCombinations->execute();
        if($selectLoveTesterCombinations->rowCount() == 1){
            $loveTesterCombination = $selectLoveTesterCombinations->fetch();
            $percentage = $loveTesterCombination['percentage'];
        }else{
            $percentage = rand(0, 100);
            $insertLoveTesterCombination = $conn->prepare("INSERT INTO loveTesterCombinations (name1, name2, percentage) VALUES (:name1, :name2, :percentage)");
            $insertLoveTesterCombination->bindParam(':name1', $name1);
            $insertLoveTesterCombination->bindParam(':name2', $name2);
            $insertLoveTesterCombination->bindParam(':percentage', $percentage);
            $insertLoveTesterCombination->execute();
        }

        $currentTimestamp = date('Y-m-d H:i:s');
        $insertIntoHistory = $conn->prepare("INSERT INTO loveTesterHistory (userId, name1, name2, percentage, createdAt) VALUES (:userId, :name1, :name2, :percentage, :createdAt)");
        $insertIntoHistory->bindParam(':userId', $userData['id']);
        $insertIntoHistory->bindParam(':name1', $name1);
        $insertIntoHistory->bindParam(':name2', $name2);
        $insertIntoHistory->bindParam(':percentage', $percentage);
        $insertIntoHistory->bindParam(':createdAt', $currentTimestamp);
        $insertIntoHistory->execute();

        $historyId = $conn->lastInsertId();

        return json_encode(array("success" => true, "percentage" => $percentage, "historyId" => $historyId));
    }

    static function clearHistory()
    {
        global $conn, $userData;

        $deleteHistory = $conn->prepare("DELETE FROM loveTesterHistory WHERE userId = :userId");
        $deleteHistory->bindParam(':userId', $userData['id']);
        $deleteHistory->execute();

        return json_encode(array("success" => true));
    }
}

?>