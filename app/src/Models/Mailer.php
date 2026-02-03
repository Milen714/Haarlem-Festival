<?php
namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mailConfig = require __DIR__ . '/../../config/mailConfig.php';

class Mailer {
    private PHPMailer $mail;
    

    public function __construct(array $mailConfig) {
        
        $this->mail = new PHPMailer(true);
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = $mailConfig['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username   = $mailConfig['username'];
        $this->mail->Password   = $mailConfig['password'];
        $this->mail->Port       = $mailConfig['port'];
        $this->mail->SMTPSecure = match ($mailConfig['encryption']) {
            'ssl' => PHPMailer::ENCRYPTION_SMTPS,
            default => PHPMailer::ENCRYPTION_STARTTLS,
        };

        $this->mail->setFrom(
            $mailConfig['from']['email'],
            $mailConfig['from']['name']
        );

        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }
    public function send(string $recipient, string $body, string $subject = 'Notification'): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($recipient);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log('Mailer error: ' . $this->mail->ErrorInfo);
            return false;
        }
    }
}