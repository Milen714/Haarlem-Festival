<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Services\PageService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);
        $this->scheduleRepository = new ScheduleRepository();
        $this->scheduleService = new ScheduleService($this->scheduleRepository);
        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);
    }

    public function index($vars = [])
    {
        $eventFilter = $_GET['event']  ?? null;  
        $dateFilter = $_GET['date'] ?? null;

        
        $pageData = $this->pageService->getPageBySlug('home');
        $schedule = $this->scheduleService->getAllSchedules(eventType: $eventFilter, date: $dateFilter);

        
        $this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData, 'schedule' => $schedule] );
    }

    #[RequireRole([UserRole::ADMIN])]
    public function adminIndex($vars = [])
    {
        $user = $this->userService->getUserById(5);
        if ($user) {
            $message = "Welcome back, " . $user->fname . "!";
        } else {
            $message = "User not found.";
        }
        $this->cmsLayout('Home/Landing', ['message' => $message, 'title' => 'The Festival Home', 'user' => $user]);
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

    public function notFound($vars = [])
    {
        http_response_code(404);
        $this->view('Errors/404', ['title' => 'Page Not Found']);
    }

    public function homePage($vars = [])
    {
        header('Content-Type: application/json');
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');

        $pageData = $this->pageService->getPageBySlug('events-jazz');
        echo json_encode($pageData);
    }
    public function YummyHome($vars = [])
    {
        $this->view('Yummy/HomePage', ['id' => 1]);
    }
    public function imageToWebp($vars = [])
    {
        $inputPath = __DIR__ . '/../../public/Assets/Home/ImagePlaceholder.png';
        $directory = __DIR__ . '/../../public/Assets/Home/';
        $outputPath = $directory . 'ImagePlaceholder.webp';
        if (!file_exists($inputPath)) {
            http_response_code(404);
            echo "Input image not found.";
            return;
        }

        // Output headers
        header('Content-Type: image/webp');

        // Output WebP directly to browser
        imagewebp(imagecreatefrompng($inputPath), $outputPath, 80);

       
    }
}