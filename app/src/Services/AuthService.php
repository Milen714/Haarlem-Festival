<?php
namespace App\Services;
require_once __DIR__ . '/../../config/config.php';
use App\Models\UserRole;
use App\Models\User;
use App\Services\Interfaces\IUserService;
use App\Services\UserService;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ILogService;
use App\Services\Interfaces\IMailService;
use App\Services\MailService;
use App\Services\LogService;
use App\config\Secrets;
use App\Exceptions\ValidationException;
use App\Exceptions\ApplicationException;
class AuthService implements IAuthService {
    private ?User $user = null;
    private ?IUserService $userService;
    private ?IMailService $mailService;
    private ILogService $logService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->mailService = new MailService();
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
        if ($this->userService !== null) {
            $this->userService->updateUser($user);
        }
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
    public function validateCaptchaToken($token) : bool
    {
        if (empty($token)) {
            throw new ValidationException("reCAPTCHA token is missing.");
        }

        $secretKey = Secrets::$reCapchaSecretKey;
        $ip = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token&remoteip=$ip";

        $request = file_get_contents($url);
        $response = json_decode($request);

        // Check if it failed OR if the score is too low
        if (!$response->success || $response->score < 0.5) {
            throw new ValidationException("reCAPTCHA verification failed. Are you a bot?");
        }

        return true;
    }

    /**
     * Register a new user with full validation and verification.
     * Handles: password validation, captcha validation, email uniqueness check,
     * token generation, user creation, and verification email sending.
     *
     * @param User $user The user to register
     * @param string $recaptchaToken The reCAPTCHA token from the form
     * @throws \Exception If validation fails or registration fails
     */
    public function registerUserWithVerification(User $user, string $recaptchaToken): void
    {
        // Validate password strength
        $passwordValidation = $this->validatePassword($user->password_hash);
        if (!$passwordValidation['valid']) {
            $errors = implode(", ", $passwordValidation['errors']);
            throw new ValidationException("Password does not meet the following criteria: {$errors}");
        }

        // Validate reCAPTCHA token
        $this->validateCaptchaToken($recaptchaToken);

        // Check if email already exists
        $existingUser = $this->userService->getUserByEmail($user->email);
        if ($existingUser) {
            throw new ValidationException("This email is already in use.");
        }

        // Generate verification token
        $token = $this->generateVerificationToken($user);

        // Build verification link
        $verificationLink = Secrets::$domain . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);

        // Save user to database
        $this->userService->createUser($user);

        // Send verification email
        $this->mailService->accountVerificationMail($user->email, $verificationLink);
    }
    

}