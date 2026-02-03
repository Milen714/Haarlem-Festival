<?php
namespace App\Repositories\Interfaces;
use App\Models\User;

interface IUserRepository {
    public function getUserById(int $id): ?User;
    public function getAllUsers(): array;
    public function getUserByEmail(string $email): ?User;
    public function createUser(User $user): bool;
    public function updateUser(User $user): bool;
}