<?php
namespace App\Services\Interfaces;
use App\Models\User;

interface IUserService {
    public function getAllUsers(): array;
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function createUser(User $user): bool;
}