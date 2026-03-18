<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Services\UserService;
use App\Services\MailService;
use App\Services\Interfaces\IAuthService;
use App\Services\AuthService;
use App\config\Secrets;
use App\Services\Interfaces\IOrderService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IMailService;
use App\Services\OrderService;


class AccountController extends BaseController
{
    private IUserService $userService;
    private IMailService $mailService;
    private IAuthService $authService;
    private IOrderService $orderService;
    public function __construct()
    {
        $this->userService = new UserService();
        $this->mailService = new MailService();
        $this->authService = new AuthService();
        $this->orderService = new OrderService();
    }
    public function login($vars = [])
    {
        $error = null;
        if (isset($_GET['error'])) {
            $error = htmlspecialchars(urldecode($_GET['error']));
        }
        $this->view('Account/Login', ['error' => $error, 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam']);
    }
    public function loginPost($vars = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            // var_dump($data);
            // die();
             if (!$data) {
                throw new \Exception('Invalid JSON input');
            }
             if (empty($data['email']) || empty($data['password'])) {
                throw new \Exception('Email and password are required.');
            }
             // Validate reCAPTCHA token    
            $user = $this->userService->authenticateUser($data['email'], $data['password']);

            if ($user) {
                $_SESSION['loggedInUser'] = $user;
                // Consolidate cart merge logic in one place.
                $this->orderService->hydrateSessionCartFormDbOnLogin($user);
                $cart = $this->orderService->getSessionCart();
                $redirect = $data['redirect'] ?? '/';
                 
                // Successful login
                //additionally check if user is admin and redirect to cms if so
                if ($user->role === UserRole::ADMIN) {
                    $this->jsonResponse(['success' => true, 'redirect' => '/cms', 'cart' => $cart ], 200);
                } else {
                    // Regular user login
                    $this->jsonResponse(['success' => true, 'redirect' => $redirect, 'cart' => $cart], 200);
                }
            } else {
                // Failed login
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Login failed. Please check your credentials and try again.',
                    'user' => ['email' => $data['email']]
                ], 401);
            }
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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
        $this->view('Account/Signup', ['title' => 'Signup Page']);
    }
    private function validateCaptchaToken($token)
    {
        if (empty($token)) {
            throw new \Exception("reCAPTCHA token is missing.");
        }

        $secretKey = Secrets::$reCapchaSecretKey;
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
            $verificationLink = Secrets::$domain . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Save the user to the database
            $this->userService->createUser($user);
            $this->mailService->accountVerificationMail($user->email, $verificationLink);
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Signup successful. Please check your email to verify your account.']);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'email')) {
                $errorMsg = "This email is already in use.";
                echo json_encode(['success' => false, 'message' => $errorMsg]);
            } else {
                $errorMsg = "An error occurred during signup: " . $e->getMessage();
                echo json_encode(['success' => false, 'message' => $errorMsg]);
            }
        }
    }
    public function forgotPassword()
    {
        $this->view('Account/ForgotPassword', ['title' => 'Forgot Password']);
    }
    public function forgotPasswordPost($vars = [])
    {
        $email = $_POST['email'] ?? '';
        try {
            $user = $this->userService->getUserByEmail($email);
            if (!$user) {
                throw new \Exception("No user found with that email address.");
            }
            $token = $this->authService->generatePasswordResetToken($user);
            $resetLink = Secrets::$domain . "/reset-password?token=" . urlencode($token) . "&email=" . urlencode($user->email);
            // Send reset email
            $this->mailService->resetPasswordMail($user->email, $resetLink);
            $this->view('Account/Login', ['success' => "Password reset email sent. Please check your inbox.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam']);
        } catch (\Exception $e) {
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => $e->getMessage()]);
        }
    }
    public function resetPassword()
    {
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
    public function resetPasswordPost($vars = [])
    {
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
            $this->view('Account/Login', ['success' => "Password has been reset successfully.", 'message' => "Please log in. now :)", 'title' => 'Login Page', 'param' => $param ?? 'noParam']);
        } catch (\Exception $e) {
            $this->view('Account/ResetPassword', [
                'title' => 'Reset Password',
                'error' => $e->getMessage(),
                "email" => $email,
                "token" => $token
            ]);
        }
    }

    public function settings($vars = [])
    {
        if (!isset($_SESSION['loggedInUser'])) {
            header("Location: /login");
            exit();
        }
        $loggedUser = $_SESSION['loggedInUser'];
        $user = $this->userService->getUserById($loggedUser->id);

        if ($user->role === UserRole::ADMIN) {
            $this->cmsLayout('Cms/Profile', ['title' => 'Admin Profile', 'user' => $user]);
        } else {
            $this->view('Account/Settings', ['title' => 'Account Settings', 'user' => $user]);
        }
    }

    public function update($vars = [])
    {
        if (!isset($_SESSION['loggedInUser'])) {
            header("Location: /login");
            exit();
        }

        try {
            $loggedUser = $_SESSION['loggedInUser'];
            $user = $this->userService->getUserById($loggedUser->id);

            $user->fromPDOData($_POST);
            $this->userService->updateUser($user);

            $_SESSION['loggedInUser'] = $user;

            if ($user->role === UserRole::ADMIN) {
                header("Location: /cms/profile");
            } else {
                header("Location: /settings");
            }
            exit();
        } catch (\Exception $e) {
            $this->view('Account/Settings', ['title' => 'Edit Account Settings', 'user' => $user, 'error' => $e->getMessage()]);
        }
    }
}