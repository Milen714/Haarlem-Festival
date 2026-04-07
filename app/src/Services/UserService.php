<?php 


namespace App\Services;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\Interfaces\IUserService;
use App\Models\User;
use App\Exceptions\ValidationException;

class UserService implements IUserService{
    private IUserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
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
    public function authenticateUser(string $email, string $password): ?User {
        $user = $this->userRepository->getUserByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }
        return null;
    }
    public function updateUser(User $user): bool {
        return $this->userRepository->updateUser($user);
    }

    public function updateUserFromRequest(User $user, array $postData): void
    {
        $fname   = trim($postData['fname']   ?? '');
        $lname   = trim($postData['lname']   ?? '');
        $email   = trim($postData['email']   ?? '');
        $phone   = trim($postData['phone']   ?? '');
        $address = trim($postData['address'] ?? '');

        if (empty($fname)) {
            throw new ValidationException("First name is required.");
        }
        if (empty($lname)) {
            throw new ValidationException("Last name is required.");
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("A valid email address is required.");
        }
        if ($phone !== '' && !preg_match('/^[+\d\s\-()\/.]{7,20}$/', $phone)) {
            throw new ValidationException("Phone number format is not valid.");
        }

        $user->fname   = $fname;
        $user->lname   = $lname;
        $user->email   = $email;
        $user->phone   = $phone   !== '' ? $phone   : null;
        $user->address = $address !== '' ? $address : null;
    }
    public function deleteUser(int $id): bool {
        return $this->userRepository->deleteUser($id);
    }
    public function findByShareToken(string $token): ?User {
        return $this->userRepository->findByShareToken($token);
    }
    public function saveShareToken(int $userId, string $token): bool {
        return $this->userRepository->saveShareToken($userId, $token);
    }
}