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
use App\ViewModels\Home\ScheduleList;

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
        try{
            $pageData = $this->pageService->getPageBySlug('home');
            $schedule = $this->scheduleService->getAllSchedules(eventType: $eventFilter, date: $dateFilter);

            $scheduleList = new ScheduleList($schedule);
            
            // foreach($pageData->content_sections[0]->gallery->media_items as $mediaItem){
            //     var_dump($mediaItem->media);
            //     echo "<br><br>";
            //     echo "<img src='" . $mediaItem->media->file_path . "' alt='" . htmlspecialchars($mediaItem->media->alt_text) . "'><br><br>";
            // }
            // die();


            $this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData, 'scheduleList' => $scheduleList] );
        } catch (\Exception $e) {
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
    }

    public function getSchedulePartial($vars = [])
    {
        $eventFilter = $_GET['event']  ?? null;  
        $dateFilter = $_GET['date'] ?? null;
        try{
            $schedule = $this->scheduleService->getAllSchedules(eventType: $eventFilter, date: $dateFilter);

            $scheduleList = new ScheduleList($schedule);
            // var_dump($scheduleList->eveningSchedules);
            // die();

            echo require_once '/app/Views/Home/Components/HomeSchedule.php';
        } catch (\Exception $e) {
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
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

        $pageData = $this->pageService->getPageBySlug('events-magic');
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