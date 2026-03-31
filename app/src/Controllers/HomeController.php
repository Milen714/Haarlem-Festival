<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\ScheduleService;
use App\Services\VenueService;
use App\Services\PageService;
use App\Services\LandmarkService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\ILandmarkService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\ViewModels\Home\ScheduleList;
use App\ViewModels\Home\StartingPoints;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;

class HomeController extends BaseController
{
    
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private IScheduleService $scheduleService;
    private IVenueService $venueService;
    private ILogService $logService;

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->venueService = new VenueService();
        $this->landmarkService = new LandmarkService();
        $this->scheduleService = new ScheduleService();
        $this->logService = new LogService();
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

            //$this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData, 'scheduleList' => $scheduleList, 'startingPoints' => $startingPoints]);
            $this->view('Home/Landing',['title' => $pageData->title, 'pageData' => $pageData, 'scheduleList' => $scheduleList, 'startingPoints' => $startingPoints]);
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

            $this->sendSuccessResponse(['success' => true, 'theme' => $theme], 200);
        } else {
            $this->sendSuccessResponse(['success' => false, 'message' => 'No theme selected'], 400);
        }
    }

    public function notFound($vars = [])
    {
        try {
            http_response_code(404);
            $this->view('Errors/404', ['title' => 'Page Not Found']);
        } catch (\Exception $e) {
            $this->logService->exception('PageNotFound', $e);
        }
    }

    public function getStartingPoints($vars = [])
    {
       
        try {
            $venues = $this->venueService->getAllVenues(); 
            $landmarks = $this->landmarkService->getAllLandmarks();
            $startingPoints = new StartingPoints($landmarks, $venues);
            echo require_once '/app/Views/Home/Components/HomeMap.php';
        } catch (\Exception $e) {
            $this->sendErrorResponse(['error' => $e->getMessage()], 500);
        }
    }
    public function getScheduleDates($vars = [])
    {
        header('Content-Type: application/json');
        try {
            $dates = $this->scheduleService->getAvailableDates();
            $this->sendSuccessResponse(['success' => true, 'dates' => $dates], 200);
        } catch (\Exception $e) {
            $this->sendSuccessResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}