<?php
namespace App\Services;
require_once __DIR__ . '/../../config/config.php';
use App\Models\UserRole;
use App\Models\User;
use App\Services\UserService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ILogService;
class AuthService implements IAuthService {
    private ?User $user = null;
    private IUserService $userService;
    private ILogService $logService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->logService  = new LogService();
    }
    public function getLoggedInUser(): ?User {
        if ($this->user === null && isset($_SESSION['loggedInUser'])) {
            //$this->user = $this->userService->getUserById($_SESSION['loggedInUser']->id);
            return $this->user;
        }
        throw new \Exception("No user logged in");
    }
   
    public function logout(string $message): void {
        session_unset();
        session_destroy();
        $this->user = null;
        header('Location: /login/' . urlencode($message));
        exit();
    }
    
    
    public function generateSecureToken(int $length = 32): string {
        $str = bin2hex(random_bytes($length));
        return base64_encode($str);
    }
    public function generatePasswordResetToken(User $user): string {
        try {
        $token = $this->generateSecureToken();
        $user->reset_token = $token;
        $user->reset_token_expiry = new \DateTime('+1 hour'); // Token valid for 1 hour
        $this->userService->updateUser($user);
        return $token;
        } catch (\Exception $e) {
            $this->logService->exception('Auth', $e);
            die("Error generating password reset token: " . $e->getMessage());
        }
    }
    public function generateVerificationToken(User $user): string {
        try {
        $token = $this->generateSecureToken();
        $user->verification_token = $token;

        return $token;
        } catch (\Exception $e) {
            $this->logService->exception('Auth', $e);
            die("Error generating verification token: " . $e->getMessage());
        }
    }
    public function validatePassword(string $password): array
{
    $result = [
        'valid' => true,
        'errors' => []
    ];

    if (strlen($password) < 8) {
        $result['valid'] = false;
        $result['errors'][] = 'At least 8 characters.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one lowercase letter.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one uppercase letter.';
    }

    if (!preg_match('/\d/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one number.';
    }

    if (!preg_match('/[\W_]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = 'At least one special character.';
    }

    return $result;
}
}