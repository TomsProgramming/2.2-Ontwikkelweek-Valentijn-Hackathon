<?php
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$selectEmails = $conn->prepare("SELECT * FROM emailQueue WHERE sent = 0 AND errorMessage IS NULL");
$selectEmails->execute();

if ($selectEmails->rowCount() > 0) {
    while ($email = $selectEmails->fetch(PDO::FETCH_ASSOC)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'shared135.cloud86-host.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'valentijnhackathon@tomtiedemann.com';
            $mail->Password   = 'QBJ2mcf*vme*ydn6tyd';
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
            $updateEmail = $conn->prepare("UPDATE emailQueue SET sent = 1 WHERE id = :id");
            $updateEmail->bindParam(':id', $id, PDO::PARAM_INT);
            $updateEmail->execute();
        } catch (Exception $e) {
            $id = $email['id'];
            $updateEmail = $conn->prepare("UPDATE emailQueue SET errorMessage = :errorMessage WHERE id = :id");
            $updateEmail->bindParam(':errorMessage', $mail->ErrorInfo, PDO::PARAM_STR);
            $updateEmail->bindParam(':id', $id, PDO::PARAM_INT);
            $updateEmail->execute();
        }
    }
}
?>