<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Repositories\LandmarkRepository;
use App\Services\VenueService;
use App\Services\PageService;
use App\Services\LandmarkService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;
use App\ViewModels\Home\ScheduleList;
use App\ViewModels\Home\StartingPoints;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private LandmarkService $landmarkService;
    private LandmarkRepository $landmarkRepository;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    private VenueRepository $venueRepository;
    private VenueService $venueService;
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
        $this->landmarkRepository = new LandmarkRepository();
        $this->landmarkService = new LandmarkService($this->landmarkRepository);
        $this->venueRepository = new VenueRepository();
        $this->venueService = new VenueService($this->venueRepository, $this->mediaService);
    }

    public function index($vars = [])
    {
        $eventFilter = $_GET['event']  ?? null;  
        $dateFilter = $_GET['date'] ?? null;
        try{
            $pageData = $this->pageService->getPageBySlug('home');
            
            $schedule = $this->scheduleService->getAllSchedules(eventType: $eventFilter, date: $dateFilter);

            $scheduleList = new ScheduleList($schedule);

            $venues = $this->venueService->getAllVenues(); 
            $landmarks = $this->landmarkService->getAllLandmarks();
            $startingPoints = new StartingPoints($landmarks, $venues);

            $this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData, 'scheduleList' => $scheduleList, 'startingPoints' => $startingPoints]);
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
            
            echo require_once '/app/Views/Home/Components/ScheduleList.php';
        } catch (\Exception $e) {
            $this->internalServerError("Error loading schedule: " . $e->getMessage());
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

    public function getStartingPoints($vars = [])
    {
       
        try {
            $venues = $this->venueService->getAllVenues(); 
            $landmarks = $this->landmarkService->getAllLandmarks();
            $startingPoints = new StartingPoints($landmarks, $venues);
            echo require_once '/app/Views/Home/Components/HomeMap.php';
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}