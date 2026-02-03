<?php
namespace App\Services;
require_once __DIR__ . '/../../config/config.php';
use App\Models\UserRole;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
class AuthService{
    private ?User $user = null;
    private UserService $userService;
    private UserRepository $userRepository;

    public function __construct()
    {   $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        
    }
    public function getLoggedInUser(): ?User {
        if ($this->user === null && isset($_SESSION['loggedInUser'])) {
            //$this->user = $this->userService->getUserById($_SESSION['loggedInUser']->id);
            return $this->user;
        }
        throw new \Exception("No user logged in");
    }
    // public function hasRole(UserRole $roleToCheck): bool {
    //     return $this->user !== null && $this->user->role === $roleToCheck;
    // }
    public function logout(string $message): void {
        session_unset();
        session_destroy();
        $this->user = null;
        header('Location: /login/' . urlencode($message));
        exit();
    }
    
    public function generateActionToken(): string{
        $token = bin2hex(random_bytes(32));
        return base64_encode($token);
        
    }
}