<?php
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$selectEmails = $conn->prepare("SELECT * FROM emailQueue WHERE sent = 0 AND errorMessage IS NULL");
$selectEmails->execute();

if ($selectEmails->rowCount() > 0) {
    while ($email = $selectEmails->fetch(PDO::FETCH_ASSOC)) {
        $mail = new PHPMailer(true);

        $currentTime = date('Y-m-d H:i:s');

        try {
            $mail->isSMTP();
            $mail->Host       = '';
            $mail->SMTPAuth   = true;
            $mail->Username   = '';
            $mail->Password   = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('valentijnhackathon@tomtiedemann.com', 'Valentijn Hackathon');
            $mail->addAddress($email['email']);

            $mail->isHTML(true);
            $mail->Subject = $email['subject'];
            $mail->Body = $email['htmlBody'];
            $mail->AltBody = $email['altBody'];

            $mail->send();
            $id = $email['id'];
            $updateEmail = $conn->prepare("UPDATE emailQueue SET sent = 1, procesTime = :procesTime WHERE id = :id");
            $updateEmail->bindParam(':id', $id, PDO::PARAM_INT);
            $updateEmail->bindParam(':procesTime', $currentTime, PDO::PARAM_STR);
            $updateEmail->execute();
        } catch (Exception $e) {
            $id = $email['id'];
            $updateEmail = $conn->prepare("UPDATE emailQueue SET errorMessage = :errorMessage, procesTime = :procesTime WHERE id = :id");
            $updateEmail->bindParam(':errorMessage', $mail->ErrorInfo, PDO::PARAM_STR);
            $updateEmail->bindParam(':id', $id, PDO::PARAM_INT);
            $updateEmail->bindParam(':procesTime', $currentTime, PDO::PARAM_STR);
            $updateEmail->execute();
        }
    }
}
?>