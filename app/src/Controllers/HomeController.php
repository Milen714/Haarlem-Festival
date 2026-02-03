<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Models\User;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
    }
    public function index($vars = [])
    {
        $user = $this->userService->getUserById(5);
        if ($user) {
            $message = "Welcome back, " . $user->fname . "!";
        } else {
            $message = "User not found.";
        }
        $this->view('Home/Landing', ['message' => $message, 'title' => 'The Festival Home', 'user' => $user] );
    }
    
    
    public function setTheme($vars = [])
    {
        header('Content-Type: application/json');
        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            // Set a cookie wit 30 day expiry for the selected theme
            setcookie('theme', $theme, time() + (86400 * 30), '/');
            
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No theme selected']);
        }
    }
}