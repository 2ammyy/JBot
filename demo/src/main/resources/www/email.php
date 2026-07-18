<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendVerificationEmail($toEmail, $toName, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jbot.noreply@gmail.com'; // Ton adresse Gmail
        $mail->Password   = 'eeazwbgrvjkgctql'; // Ton mot de passe d'application
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Expéditeur et destinataire
        $mail->setFrom('jbot.noreply@gmail.com', 'JBot');
        $mail->addAddress($toEmail, $toName);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = ' Code de verification';
        $mail->Body    = "Bonjour ,<br><br>Voici votre code de verification : <b>$otpCode</b><br><br>Merci !";

        $mail->send();
        echo "✅ Email envoyé à $toEmail";
    } catch (Exception $e) {
        echo "❌ Erreur d'envoi : {$mail->ErrorInfo}";
    }
}
?>
