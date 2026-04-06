<?php

namespace App\Controllers;

use App\Framework\BaseController;
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
use App\Services\Interfaces\ILogService;
use App\Services\OrderService;
use App\Services\LogService;
use App\Exceptions\UserFacingException;
use App\Exceptions\ValidationException;

class AccountController extends BaseController
{
    private IUserService $userService;
    private IMailService $mailService;
    private IAuthService $authService;
    private IOrderService $orderService;
    private ILogService $logService;
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->mailService = new MailService();
        $this->orderService = new OrderService();
        $this->logService = new LogService();
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
        try {
            $data = $this->getPostData();
            
             if (!$data) {
                $this->sendErrorResponse('Invalid JSON payload.', 400);
                return;
            }
             if (empty($data['email']) || empty($data['password'])) {
                $this->sendErrorResponse('Email and password are required.', 400);
                return;
            }
               
            $user = $this->userService->authenticateUser($data['email'], $data['password']);

            if ($user) {
                $this->setLoggedInUser($user); // Store user in session upon successful login
                // Consolidate cart merge logic in one place.
                $this->orderService->hydrateSessionCartFormDbOnLogin($user);
                $redirect = $data['redirect'] ?? '/';
                 
                // Successful login
                //additionally check if user is admin and redirect to cms if so
                if ($user->role === UserRole::ADMIN) {
                    $redirectAdmin = '/cms';
                    if($redirect === '/payment-details') {
                        $redirectAdmin = '/payment-details';
                    }
                    $this->sendSuccessResponse(['success' => true, 'redirect' => $redirectAdmin ], 200);
                }
                 else if($user->role === UserRole::EMPLOYEE) {
                    $this->sendSuccessResponse(['success' => true, 'redirect' => '/qr-code/scan' ], 200);
                } else {
                    // Regular user login
                    $this->sendSuccessResponse(['success' => true, 'redirect' => $redirect], 200);
                }
            } else {
                // Failed login
                $this->sendSuccessResponse([
                    'success' => false,
                    'message' => 'Login failed. Please check your credentials and try again.',
                    'user' => ['email' => $data['email']]
                ], 401);
            }
        } catch (UserFacingException $e) {
            $this->logService->info('Account', 'User-facing error: ' . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An error occurred during login.',
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
    public function signupPost($vars = [])
    {

        $data = $this->getPostData();
        $user = new User();
        $user = $user->fromArray($data);

        try {
            $recaptchaToken = $data['recaptcha'] ?? '';
            $this->authService->registerUserWithVerification($user, $recaptchaToken);

            $this->sendSuccessResponse([
                'success' => true,
                'message' => 'Signup successful. Please check your email to verify your account.',
            ], 201);
        } catch (ValidationException $e) {
            $this->logService->info('Account', 'Validation error: ' . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (UserFacingException $e) {
            $this->logService->info('Account', 'User-facing error: ' . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An error occurred during signup.'
            ], 500);
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
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => 'An error occurred. Please try again.']);
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
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            $this->view('Account/ForgotPassword', ['title' => 'Forgot Password', 'error' => 'An error occurred. Please try again.']);
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
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            $this->view('Account/ResetPassword', [
                'title' => 'Reset Password',
                'error' => 'An error occurred. Please try again.',
                "email" => $email,
                "token" => $token
            ]);
        }
    }

    public function settings($vars = [])
    {
        $loggedUser = $this->getLoggedInUser();
        if (!isset($loggedUser)) {
            header("Location: /login");
            exit();
        }
        try {
            $user = $this->userService->getUserById($loggedUser->id);

            if ($user->role === UserRole::ADMIN) {
                $this->cmsLayout('Cms/Profile', ['title' => 'Admin Profile', 'user' => $user]);
            } else {
                $this->view('Account/Settings', ['title' => 'Account Settings', 'user' => $user]);
            }
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            header("Location: /login");
            exit();
        }
    }

    public function update($vars = [])
    {
        $loggedUser = $this->getLoggedInUser();
        if (!isset($loggedUser)) {
            $this->redirect('/login');
            return;
        }

        try {
            $user = $this->userService->getUserById($loggedUser->id);
            $user->fromPDOData($_POST);
            $this->userService->updateUser($user);
            $this->setLoggedInUser($user);

            if ($user->role === UserRole::ADMIN) {
                $_SESSION['success'] = 'Profile updated successfully.';
                $this->redirect('/cms/profile');
            } else {
                $_SESSION['success'] = 'Settings updated successfully.';
                $this->redirect('/settings');
            }
        } catch (\Throwable $e) {
            $this->logService->exception('Account', $e);
            if ($loggedUser->role === UserRole::ADMIN) {
                $this->cmsLayout('Cms/Profile', ['title' => 'Admin Profile', 'user' => $loggedUser, 'error' => 'An error occurred. Please try again.']);
            } else {
                $this->view('Account/Settings', ['title' => 'Account Settings', 'user' => $loggedUser, 'error' => 'An error occurred. Please try again.']);
            }
        }
    }
}