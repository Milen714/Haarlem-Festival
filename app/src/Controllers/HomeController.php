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
use App\ViewModels\Home\LandingPageViewModel;
use App\ViewModels\MapMarker;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
use App\Exceptions\UserFacingException;

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

    /**
     * Display the home landing page with schedules, venues, and landmarks.
     * Retrieves filtered schedule data based on event type and date query parameters.
     * Falls back to 500 error if page or schedule data cannot be loaded.
     */
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

            $landingPageViewModel = new LandingPageViewModel($pageData, $scheduleList, $landmarks, $venues);

            $this->view('Home/Landing', ['title' => $pageData->title, 'pageViewModel' => $landingPageViewModel]);
        } catch (UserFacingException $e) {
            $this->logService->info('Home', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the homepage.');
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->internalServerError('An error occurred while loading the homepage.');
        }
    }

    /**
     * Returns a partial HTML view of the schedule list for dynamic filtering.
     * Respects event type and date filter parameters from GET request.
     */
    public function getSchedulePartial($vars = [])
    {
        $eventFilter = $_GET['event']  ?? null;  
        $dateFilter = $_GET['date'] ?? null;
        try{
            $schedule = $this->scheduleService->getAllSchedules(eventType: $eventFilter, date: $dateFilter);

            $scheduleList = new ScheduleList($schedule);
            
            echo require_once '/app/Views/Home/Components/ScheduleList.php';
        } catch (UserFacingException $e) {
            $this->logService->info('Home', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the schedule.');
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->internalServerError('An error occurred while loading the schedule.');
        }
    }

    /**
     * Sets the user's theme preference via cookie.
     * Expects a POST parameter 'theme' with the theme name.
     */
    public function setTheme($vars = [])
    {
        try {
            if (!isset($_POST['theme'])) {
                $this->sendSuccessResponse(['success' => false, 'message' => 'No theme selected'], 400);
                return;
            }
            
            $theme = $_POST['theme'];
            // Set a cookie with 30 day expiry for the selected theme
            setcookie('theme', $theme, time() + (86400 * 30), '/');

            $this->sendSuccessResponse(['success' => true, 'theme' => $theme], 200);
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while setting the theme.'], 500);
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

    /**
     * Returns a partial HTML view of the map with venues and landmarks as starting points.
     */
    public function getStartingPoints($vars = [])
    {
       
        try {
            $venues = $this->venueService->getAllVenues(); 
            $landmarks = $this->landmarkService->getAllLandmarks();
            $startingPoints = new StartingPoints($landmarks, $venues);
            echo require_once '/app/Views/Home/Components/HomeMap.php';
        } catch (UserFacingException $e) {
            $this->logService->info('Home', 'User-facing error: ' . $e->getMessage());
            $this->sendErrorResponse(['error' => 'An error occurred while loading the map.'], 500);
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->sendErrorResponse(['error' => 'An error occurred while loading the map.'], 500);
        }
    }
    /**
     * Returns available festival dates as a JSON array.
     * Used for populating date filters on the frontend.
     */
    public function getScheduleDates($vars = [])
    {
        try {
            $dates = $this->scheduleService->getAvailableDates();
            $this->sendSuccessResponse(['success' => true, 'dates' => $dates], 200);
        } catch (UserFacingException $e) {
            $this->logService->info('Home', 'User-facing error: ' . $e->getMessage());
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading dates.'], 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading dates.'], 500);
        }
    }
    public function getVenues($vars = [])
    {
        try {
            $venues = $this->venueService->getAllVenues();
            $landmarks = $this->landmarkService->getAllLandmarks();
            
            // Combine venues and landmarks into MapMarker objects
            $markers = [];
            
            foreach ($venues as $venue) {
                $marker = new MapMarker($venue);
                $markers[] = $marker->toArray();
            }
            
            foreach ($landmarks as $landmark) {
                $marker = new MapMarker($landmark);
                $markers[] = $marker->toArray();
            }
            
            $this->sendSuccessResponse(['success' => true, 'markers' => $markers, 'venues' => $venues, 'landmarks' => $landmarks], 200);
        } catch (UserFacingException $e) {
            $this->logService->info('Home', 'User-facing error: ' . $e->getMessage());
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading venues.'], 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Home', $e);
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading venues.'], 500);
        }
    }
}