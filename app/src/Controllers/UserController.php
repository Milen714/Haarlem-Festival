<?php

namespace App\Controllers;
use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Services\UserService;
use App\Models\User;
use App\Middleware\RequireRole;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\IUserService;
use App\Services\AuthService;


class UserController extends BaseController
{
    private IUserService $userService;
    private IAuthService $authService;
    public function __construct(?IUserService $userService = null, ?IAuthService $authService = null)
    {
        $this->userService = $userService ?? new UserService();
        $this->authService = $authService ?? new AuthService($this->userService);
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
        } catch (\Exception $e) {
            error_log("User list error: " . $e->getMessage());
            // Don't redirect - show error on the same page
            $this->cmsLayout('Cms/Users/Index', [
                'title' => 'Manage Users',
                'users' => [],
                'error' => 'Failed to load users: ' . $e->getMessage()
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
        } catch (\Exception $e) {
            error_log("User create form error: " . $e->getMessage());
            $this->cmsLayout('Cms/Users/Form', [
                'title' => 'Create New User',
                'user' => null,
                'action' => '/cms/users/store',
                'error' => 'Failed to load form: ' . $e->getMessage()
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
                throw new \Exception("Email and Password are required.");
            }
            $existingUser = $this->userService->getUserByEmail($user->email);
            if ($existingUser) {
                throw new \Exception("This email is already in use.");
            }
                
            $this->userService->createUser($user);
            $_SESSION['success'] = "User '{$user->email}' created successfully.";
            header('Location: /cms/users');
            exit();
        } catch (\Exception $e) {
            error_log("User store error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create user: ' . $e->getMessage();
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
                throw new \Exception("User not found.");
            }
            $this->cmsLayout('Cms/Users/Form', [
                'title' => 'Edit User: ' . $user->email,
                'user' => $user,
                'action' => "/cms/users/update/{$userId}"
            ]);
        } catch (\Exception $e) {
            error_log("User edit error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load user: ' . $e->getMessage();
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
                throw new \Exception("User not found.");
            }

            $data = $_POST;

            $user->is_active = isset($_POST['is_active']);
            $user->is_verified = isset($_POST['is_verified']);
            if (!empty($data['password'])) {
                $passwordValidation = $this->authService->validatePassword($user->password_hash);
                if (!$passwordValidation['valid']) {
                    $errorMsg = "Password does not meet the following criteria: " . implode(", ", $passwordValidation['errors']);
                    throw new \Exception($errorMsg);
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
        } catch (\Exception $e) {
            error_log("User update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update user: ' . $e->getMessage();
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
                throw new \Exception("User not found.");
            }
            $this->userService->deleteUser($userId);
            $_SESSION['success'] = "User deleted successfully.";
            header('Location: /cms/users');
            exit();
        } catch (\Exception $e) {
            error_log("User delete error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete user: ' . $e->getMessage();
            header('Location: /cms/users');
            exit();
        }
    }

}