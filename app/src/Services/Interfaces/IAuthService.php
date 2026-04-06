<?php 
namespace App\Services\Interfaces;

use App\Models\User;
interface IAuthService {
    public function getLoggedInUser(): ?User;
    public function logout(string $message): void;
    public function generateSecureToken(int $length = 32): string;
    public function generatePasswordResetToken(User $user): string;
    public function generateVerificationToken(User $user): string;
    function validatePassword(string $password): array;

}