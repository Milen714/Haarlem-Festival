<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Mailer;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\MailService;
use App\Services\Interfaces\IAuthService;
use App\Services\AuthService;
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

class AccountController extends BaseController {
    private UserService $userService;
    private UserRepository $userRepository;
    private MailService $mailService;
    private IAuthService $authService;
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->mailService = new MailService();
        $this->authService = new AuthService();
    }
    public function login($vars = [])
    {
        $error = null;
        if (isset($_GET['error'])) {
            $error = htmlspecialchars(urldecode($_GET['error']));
        }
        $this->view('Account/Login', ['error' => $error, 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
    }
    public function loginPost($vars = [])
    {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = $this->userService->authenticateUser($email, $password);
        if ($user) {
            $_SESSION['loggedInUser'] = $user;
            if($user->role === UserRole::ADMIN) {
                header("Location: /cms");
            }else{
                // Successful login
            
            header("Location: /");
            exit();
            
            }
            
        } else {
            // Failed login
            throw new \Exception("Invalid email or password.");
        }
        } catch (\Exception $e) {
            //header("Location: /login/" . urlencode($e->getMessage()));
            $this->view('Account/Login', ['error' => $e->getMessage(), 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
            exit();
        }
    }
    public function logout($vars = [])
    {
        // Clear the session to log out the user
        session_unset();
        session_destroy();
        header("Location: /");
        
        exit();
    }
    public function signup($vars = [])
    {
        $this->view('Account/Signup', ['title' => 'Signup Page'] );
    }
    private function validateCaptchaToken($token) {
    if (empty($token)) {
        throw new \Exception("reCAPTCHA token is missing.");
    }

    $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token&remoteip=$ip";
    
    $request = file_get_contents($url);
    $response = json_decode($request);        
    
    // Check if it failed OR if the score is too low
    if (!$response->success || $response->score < 0.5) {
        throw new \Exception("reCAPTCHA verification failed. Are you a bot?");
    }
    
    return true;
}
    public function signupPost($vars = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $user = new User();
        $user = $user->fromArray($data);

        try {
            // validate password strength
            $passwordValidation = $this->authService->validatePassword($user->password_hash);
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new \Exception($errorMsg);
            }

            // Validate reCAPTCHA token
            $token = $data['recaptcha'] ?? ''; 
            $this->validateCaptchaToken($token);
            // Check if email already exists
            $existingUser = $this->userService->getUserByEmail($user->email);
            if ($existingUser) {
                throw new \Exception("This email is already in use.");
            }
            // Generate verification token and send verification email
            $token = $this->authService->generateVerificationToken($user);
            $verificationLink = $_ENV['DOMAIN_URL'] . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Save the user to the database
            $this->userService->createUser($user);
            $this->mailService->accountVerificationMail($user->email, $verificationLink);
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Signup successful. Please check your email to verify your account.']);
        } catch (\Exception $e) {
            if(str_contains($e->getMessage(), 'email')) {
                $errorMsg = "This email is already in use.";
                echo json_encode(['success' => false, 'message' => $errorMsg]);
            } else {
                $errorMsg = "An error occurred during signup: " . $e->getMessage();
                echo json_encode(['success' => false, 'message' => $errorMsg]);
            }
            
        }
    }
    public function forgotPassword() {
        $this->view('Account/ForgotPassword', ['title' => 'Forgot Password']);
    }
    public function forgotPasswordPost($vars = []) {
        $email = $_POST['email'] ?? '';
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new \Exception("No user found with that email address.");
            }
            $token = $this->authService->generatePasswordResetToken($user);
            $resetLink = $_ENV['DOMAIN_URL'] . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Send reset email
            $this->mailService->resetPasswordMail($user->email, $resetLink);
            $this->view('Account/Login', ['success' => "Password reset email sent. Please check your inbox.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
            
            
        } catch (\Exception $e) {
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => $e->getMessage()]);
        }
    }
    public function resetPassword() {
        try {
            $token = $_GET['token'] ?? '';
            $email = $_GET['email'] ?? '';
            $user = $this->userService->getUserByEmail($email);
            if (!$user || $user->reset_token !== $token) {
                throw new \Exception("Invalid or expired password reset token.");
            }
            $now = new \DateTime();
            if ($user->reset_token_expiry < $now) {
                throw new \Exception("Password reset token has expired.");
            }
            // Show reset password form
            $this->view('Account/ResetPassword', ['title' => 'Reset Password']);
        } catch (\Exception $e) {
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => $e->getMessage()]);
        }
        // Reset password logic here
    }
    public function resetPasswordPost($vars = []) {
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $repeatPassword = $_POST['repeatPassword'] ?? '';
        // Validate passwords Match
        if ($newPassword !== $repeatPassword) {
            throw new \Exception("Passwords do not match. Please try again.");
        }
        // validate password strength
            $passwordValidation = $this->authService->validatePassword($newPassword);
            if (!$passwordValidation['valid']) {
                $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                throw new \Exception($errorMsg);
            }
            
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user || $user->reset_token !== $token) {
                throw new \Exception("Invalid or expired password reset token.");
            }
            $now = new \DateTime();
            if ($user->reset_token_expiry < $now) {
                throw new \Exception("Password reset token has expired.");
            }
            // Update the user's password
            $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
            // Clear the reset token and expiry
            $user->reset_token = null;
            $user->reset_token_expiry = null;
            $this->userService->updateUser($user);
            // Redirect to login with success message
            $this->view('Account/Login', ['success' => "Password has been reset successfully.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
        } catch (\Exception $e) {
            $this->view('Account/ResetPassword', ['title' => 'Reset Password', 'error' => $e->getMessage(),
                        "email" => $email, "token" => $token]);
        }
    }
    
    public function settings($vars = []) {
        if (!isset($_SESSION['loggedInUser'])) {
            header("Location: /login");
            exit();
        }
        $loggedUser = $_SESSION['loggedInUser'];
        $user = $this->userService->getUserById($loggedUser->id);

        $this->view('Account/Settings', ['title' => 'Account Settings', 'user' => $user]);
    }

    public function update($vars = []) {
        if (!isset($_SESSION['loggedInUser'])) {
            header("Location: /login");
            exit();
        }

        try {
            $loggedUser = $_SESSION['loggedInUser'];
            $user = $this->userService->getUserById($loggedUser->id);

            $user->mapUser($_POST);
            $this->userService->updateUser($user);

            $_SESSION['loggedInUser'] = $user;

            header("Location: /settings");
            exit(); 

        } catch (\Exception $e) {
            $this->view('Account/Settings', ['title' => 'Edit Account Settings', 'user' => $user, 'error' => $e->getMessage()]);
        }
    }
}