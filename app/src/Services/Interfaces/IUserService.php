<?php
namespace App\Services\Interfaces;
use App\Models\User;

interface IUserService {
    public function getAllUsers(): array;
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function createUser(User $user): bool;
    public function authenticateUser(string $email, string $password): ?User;
    public function updateUser(User $user): bool;
    public function updateUserFromRequest(User $user, array $postData): void;
    public function deleteUser(int $id): bool;
    public function findByShareToken(string $token): ?User;
    public function saveShareToken(int $userId, string $token): bool;
}