<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Services\RestaurantService;
use App\Services\PageService;
use App\Services\VenueService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\ICuisineService;
use App\Models\Yummy\RestaurantListViewModel;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\MediaService;
use App\Services\CuisineService;

class YummyController extends BaseController
{
    private IUserService $userService;
    private IPageService $pageService;
    private IMediaService $mediaService;
    private IScheduleService $scheduleService;
    private IRestaurantService $restaurantService;
    private IVenueService $venueService;
    private ICuisineService $cuisineService;
    
    public function __construct()
    {
        $this->userService = new UserService();
        $this->pageService = new PageService();
        $this->mediaService = new MediaService();
        $this->restaurantService = new RestaurantService();
        $this->venueService = new VenueService();
        $this->scheduleService = new ScheduleService();
        $this->cuisineService = new CuisineService();
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
            
            
            if (!$pageData) {
                error_log("Yummy page data not found for slug: events-yummy");
                $this->notFound();
                return;
            }
            //var_dump($pageData->event_category->event_id);
            //die();
            
            $venues = $this->venueService->getVenuesByEventId($pageData->event_category->event_id);
            $restaurants = $this->restaurantService->getRestaurantsByEventId($pageData->event_category->event_id);
            $gallery = null;

            foreach ($pageData->content_sections as $section) {
                if (!empty($section->gallery_id)) {
                    $gallery = $this->mediaService->getGalleryById($section->gallery->gallery_id);
                    break;
                }
            }

            $galleryItems = $gallery->media_items ?? [];
            
            $this->view('Yummy/index', [
                'title' => $pageData->title ?? 'Yummy Event',
                'pageData' => $pageData,
                'sections' => $pageData->content_sections,
                'venues' => $venues,
                'restaurants' => $restaurants,
                'galleryItems' => $galleryItems
            ]);
        } catch (\Exception $e) {
            error_log("Error in YummyController index method: " . $e->getMessage());
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
    }
    
    public function displayRestaurants(){
        try{
            $pageData = $this->pageService->getPageBySlug('events-yummy-restaurants');
            
            
            if (!$pageData) {
                error_log("Yummy page data not found for slug: events-yummy-restaurants");
                $this->notFound();
                return;
            }

            $eventId = $pageData->event_category->event_id;
            $cuisineId = isset($_GET['cuisine']) ? (int)$_GET['cuisine'] : null;
         
            $restaurants = $this->restaurantService->getAllRestaurants($eventId, $cuisineId);
            $cuisines = $this->cuisineService->getCuisines();

            $viewModel = new RestaurantListViewModel(
                $pageData,
                $restaurants,
                $cuisines,
                $cuisineId
            );

            $this->view('Yummy/restaurants', [
                'viewModel' => $viewModel
            ]);

             
        }
        catch(\Exception $e){
            error_log("Error in YummyController index method: " . $e->getMessage());
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
    }

    public function restaurantDetail($vars = []){
        try{
            $restaurantId = (int)($vars['id'] ?? 0);
            $pageData = $this->pageService->getPageBySlug('events-yummy-restaurants-restaurant');
            
            
            if (!$pageData) {
                error_log("Yummy page data not found for slug: events-yummy-restaurants");
                $this->notFound();
                return;
            }
            $restaurant = $this->restaurantService->getRestaurantById($restaurantId);
            $schedules = $this->scheduleService->getSchedulesByRestaurant($restaurantId);
            //because it should display 3 sessions from the schedule, so grouped by date
            $groupedSchedules = [];
            foreach($schedules as $schedule){
                $date = $schedule->date->format('Y-m-d');
                $groupedSchedules[$date][] = $schedule;
            }
            if (!$restaurant) {
                $this->notFound();
                return;
            }

            $this->view('Yummy/DetailPage', [
                'restaurant' => $restaurant,
                'pageData' => $pageData,
                'groupedSchedules' => $groupedSchedules,
                'schedules' => $schedules
            ]);
        }
        catch(\Exception $e){
            error_log("Error in YummyController index method: " . $e->getMessage());
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
        }
    }
}