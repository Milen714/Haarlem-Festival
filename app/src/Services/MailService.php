<?php
namespace App\Services;
use App\Services\Interfaces\IMailService;
use App\Models\Mailer;
use App\Models\BookSwapRequest;
class MailService implements IMailService {
    public function sendEmail(string $to, string $subject, string $body): void {
        $mailConfig = require __DIR__ . '/../../config/mailConfig.php';
        $mailer = new Mailer($mailConfig);
        $mailer->send($to, $body, $subject);
    }
    public function resetPasswordMail(string $to, string $resetLink): void {
        $subject = "The Festival Password Reset Request";
        $body = "<h1>Password Reset Request</h1>
                 <p>Click the link below to reset your password:</p>
                 <a href='" . htmlspecialchars($resetLink) . "'>Reset Password</a>
                 <p>This link will expire in 1 hour.</p>";
        $this->sendEmail($to, $subject, $body);
    }
    public function accountVerificationMail(string $to, string $verificationLink): void {
        $subject = "The Festival Account Verification";
        $body = "<h1>Account Verification</h1>
                 <p>Click the link below to verify your account:</p>
                 <a href='" . htmlspecialchars($verificationLink) . "'>Verify Account</a>
                 <p>Welcome to The Festival!</p>";
        $this->sendEmail($to, $subject, $body);
    }
}