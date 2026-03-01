<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\VenueRepository;
use App\Services\RestaurantService;
use App\Services\PageService;
use App\Services\VenueService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;

class YummyController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    private RestaurantService $restaurantService;
    private RestaurantRepository $restaurantRepository;
    private VenueService $venueService;
    private VenueRepository $venueRepository;

    
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
        $this->restaurantRepository = new RestaurantRepository();
        $this->restaurantService = new RestaurantService($this->restaurantRepository);
        $this->venueRepository = new VenueRepository();
        $this->venueService = new VenueService($this->venueRepository, $this->mediaService);
    }
    public function index()
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug('events-yummy');
            
            
            if (!$pageData) {
                error_log("Yummy page data not found for slug: {$slug}");
                $this->notFound();
                return;
            }
            
            $venues = $this->venueService->getVenuesByEventId($pageData->event_category->event_id);
            $restaurants = $this->restaurantService->getRestaurantsByEventId($pageData->event_category->event_id);
            
            $this->view('Yummy/index', [
                'title' => $pageData->title ?? 'Yummy Event',
                'pageData' => $pageData,
                'sections' => $pageData->content_sections,
                'venues' => $venues,
                'restaurants' => $restaurants,
            ]);
        } catch (\Exception $e) {
            error_log("Error in YummyController index method: " . $e->getMessage());
            $this->notFound();
        }
    }
    public function yummy()
    {
        //$slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug('events-yummy');
            
            
            // if (!$pageData) {
            //     error_log("Yummy page data not found for slug: {$slug}");
            //     $this->notFound();
            //     return;
            // }
            // var_dump($pageData->event_category->event_id);
            // die();
            
            $venues = $this->venueService->getVenuesByEventId($pageData->event_category->event_id);
            $restaurants = $this->restaurantService->getRestaurantsByEventId($pageData->event_category->event_id);
            
            $this->view('Yummy/index', [
                'title' => $pageData->title ?? 'Yummy Event',
                'pageData' => $pageData,
                'sections' => $pageData->content_sections,
                'venues' => $venues,
                'restaurants' => $restaurants,
            ]);
        } catch (\Exception $e) {
            error_log("Error in YummyController index method: " . $e->getMessage());
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
    }
}