<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/credentials.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function send_email($to_email, $subject, $html_body, $text_body = null) {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from_name;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $smtp_port;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($smtp_username, $smtp_from_name);
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = $text_body ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html_body));

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log("savmrl mailer error: " . $e->getMessage());
        return false;
    }
}
