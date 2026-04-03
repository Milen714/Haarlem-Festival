<?php

namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
interface IMailService {
    public function sendEmail(string $to, string $subject, string $body, array $attachments = []): void;
    public function resetPasswordMail(string $to, string $resetLink): void;
    public function accountVerificationMail(string $to, string $verificationLink): void;
    public function ticketsPurchasedMail(string $to, string $orderDetails): void;

}