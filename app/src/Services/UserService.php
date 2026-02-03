<?php 


namespace App\Services;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\Interfaces\IUserService;
use App\Models\User;
class UserService implements IUserService{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }
    public function getUserById(int $id): ?User {
        return $this->userRepository->getUserById($id);
    }

    public function getAllUsers(): array {
        return $this->userRepository->getAllUsers();
    }

    public function getUserByEmail(string $email): ?User {
        return $this->userRepository->getUserByEmail($email);
    }

    public function createUser(User $user): bool {
        return $this->userRepository->createUser($user);
    }
}