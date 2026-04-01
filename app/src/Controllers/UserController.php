<?php

namespace App\Controllers;
use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Services\UserService;
use App\Models\User;
use App\Middleware\RequireRole;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\ILogService;
use App\Services\AuthService;
use App\Services\LogService;
use App\Exceptions\UserFacingException;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;


class UserController extends BaseController
{
    private IUserService $userService;
    private IAuthService $authService;
    private ILogService $logService;
    public function __construct()
    {
        $this->userService = new UserService();
        $this->authService = new AuthService();
        $this->logService  = new LogService();
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {        
        try {
            $users = $this->userService->getAllUsers();

            $this->cmsLayout('Cms/Users/Index', [
                'title' => 'Manage Users',
                'users' => $users
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            // Don't redirect - show error on the same page
            $this->cmsLayout('Cms/Users/Index', [
                'title' => 'Manage Users',
                'users' => [],
                'error' => 'Failed to load users.'
            ]); 
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        try {
            $this->cmsLayout('Cms/Users/Form', [
                'title' => 'Create New User',
                'user' => null,
                'action' => '/cms/users/store'
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            $this->cmsLayout('Cms/Users/Form', [
                'title' => 'Create New User',
                'user' => null,
                'action' => '/cms/users/store',
                'error' => 'Failed to load form.'
            ]);
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        try {
            $data = $_POST;
            $data['is_active'] = isset($_POST['is_active']);
            $data['is_verified'] = isset($_POST['is_verified']);

            $user = new User();
            $user = $user->fromArray($data);
            
            if (empty($user->email) || empty($data['password'])) {
                throw new ValidationException("Email and Password are required.");
            }
            $existingUser = $this->userService->getUserByEmail($user->email);
            if ($existingUser) {
                throw new ValidationException("This email is already in use.");
            }
                
            $this->userService->createUser($user);
            $_SESSION['success'] = "User '{$user->email}' created successfully.";
            header('Location: /cms/users');
            exit();
        } catch (ValidationException $e) {
            $this->logService->info('User', 'Validation error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /cms/users/create');
            exit();
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            $_SESSION['error'] = 'Failed to create user.';
            header('Location: /cms/users/create');
            exit();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $userId = (int)($vars['id'] ?? 0);
        try {
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                throw new ResourceNotFoundException("User not found.");
            }
            $this->cmsLayout('Cms/Users/Form', [
                'title' => 'Edit User: ' . $user->email,
                'user' => $user,
                'action' => "/cms/users/update/{$userId}"
            ]);
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('User', 'Resource not found: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /cms/users');
            exit();
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            $_SESSION['error'] = 'Failed to load user.';
            header('Location: /cms/users');
            exit(); 
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $userId = (int)($vars['id'] ?? 0);
        try {
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                throw new ResourceNotFoundException("User not found.");
            }

            $data = $_POST;

            $user->is_active = isset($_POST['is_active']);
            $user->is_verified = isset($_POST['is_verified']);
            if (!empty($data['password'])) {
                $passwordValidation = $this->authService->validatePassword($user->password_hash);
                if (!$passwordValidation['valid']) {
                    $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                    throw new ValidationException($errorMsg);
                }
                $user->password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            $user->fname = $data['fname'] ?? $user->fname;
            $user->lname = $data['lname'] ?? $user->lname;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->address = $data['address'] ?? $user->address;
            $user->role = \App\Models\Enums\UserRole::from($data['role']);
            $this->userService->updateUser($user);

            $_SESSION['success'] = "User '{$user->email}' updated successfully.";
            $this->redirect('/cms/users');
        } catch (ValidationException | ResourceNotFoundException $e) {
            $this->logService->info('User', 'Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/users/edit/' . $userId);
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            $_SESSION['error'] = 'Failed to update user.';
            $this->redirect('/cms/users/edit/' . $userId);
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $userId = (int)($vars['id'] ?? 0);
        try {
            $user = $this->userService->getUserById($userId);
            if (!$user) {
                throw new ResourceNotFoundException("User not found.");
            }
            $this->userService->deleteUser($userId);
            $_SESSION['success'] = "User deleted successfully.";
            header('Location: /cms/users');
            exit();
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('User', 'Resource not found: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /cms/users');
            exit();
        } catch (\Throwable $e) {
            $this->logService->exception('User', $e);
            $_SESSION['error'] = 'Failed to delete user.';
            header('Location: /cms/users');
            exit();
        }
    }

}