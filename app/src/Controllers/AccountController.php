<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Mailer;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\MailService;

class AccountController extends BaseController {
    private UserService $userService;
    private UserRepository $userRepository;
    private MailService $mailService;
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->mailService = new MailService();
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
            // Successful login
            $_SESSION['loggedInUser'] = $user;
            header("Location: /");
            exit();
            
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
    public function signupPost($vars = [])
    {
        $user = new User();
        $user = $user->fromPost();
        try {
            // Check if email already exists
            $existingUser = $this->userService->getUserByEmail($user->email);
            if ($existingUser) {
                throw new \Exception("This email is already in use.");
            }
            
            $token = $this->generateVerificationToken($user);
            require_once '../config/secrets.php';
            $verificationLink = $DOMAIN_URL . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            $this->userService->createUser($user);
            $this->mailService->accountVerificationMail($user->email, $verificationLink);
            $this->view('Account/Login', ['success' => "Signup successful for " . htmlspecialchars($user->fname) . " with email " . htmlspecialchars($user->email) . ".", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
        } catch (\Exception $e) {
            if(str_contains($e->getMessage(), 'email')) {
                $errorMsg = "This email is already in use.";
            } else {
                $errorMsg = $e->getMessage();
            }
            $this->view('Account/Signup', ['title' => 'Signup Page', 'userModel' => $user, 'error' => $errorMsg] );
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
            $token = $this->generatePasswordResetToken($user);
            require_once '../config/secrets.php';
            $resetLink = $DOMAIN_URL . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
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
        if ($newPassword !== $repeatPassword) {
            $this->view('Account/ResetPassword', ['title' => 'Reset Password', 'error' => "Passwords do not match.",
                        "email" => $email, "token" => $token]);
            return;
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
            $this->view('Home/Login', ['success' => "Password has been reset successfully.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam'] );
        } catch (\Exception $e) {
            $this->view('Account/ResetPassword', ['title' => 'Reset Password', 'error' => $e->getMessage()]);
        }
    }
    
    private function generateSecureToken(int $length = 32): string {
        $str = bin2hex(random_bytes($length));
        return base64_encode($str);
    }
    private function generatePasswordResetToken(User $user): string {
        try {
        $token = $this->generateSecureToken();
        $user->reset_token = $token;
        $user->reset_token_expiry = new \DateTime('+1 hour'); // Token valid for 1 hour
        $this->userService->updateUser($user);
        return $token;
        } catch (\Exception $e) {
            die("Error generating password reset token: " . $e->getMessage());
        }
    }
    private function generateVerificationToken(User $user): string {
        try {
        $token = $this->generateSecureToken();
        $user->verification_token = $token;
        
        return $token;
        } catch (\Exception $e) {
            die("Error generating verification token: " . $e->getMessage());
        }
    }
}