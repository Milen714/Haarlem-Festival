<?php 

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Repositories\MediaRepository;
use App\Repositories\PageRepository;
use App\Repositories\VenueRepository;
use App\Repositories\RestaurantRepository;
use App\Services\MediaService;
use App\Services\PageService;
use App\Services\RestaurantService;
use App\Services\VenueService;

class YummyController extends BaseController
{
    private PageService $pageService;
    private PageRepository $pageRepository;
    private VenueService $venueService;
    private RestaurantService $restaurantService;

    private const YUMMY_EVENT_ID = 1;
    private const YUMMY_SLUG = 'events-yummy';

    
    public function __construct()
    {
        $mediaRepository = new MediaRepository();
        $mediaService = new MediaService($mediaRepository);

        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);

        $venueRepository = new VenueRepository();
        $this->venueService = new VenueService($venueRepository, $mediaService);

        $restaurantRepository = new RestaurantRepository();
        $this->restaurantService = new RestaurantService($restaurantRepository);

    }
    public function index()
    {
        error_log('YummyController::index called');
        try {
            $slug = self::YUMMY_SLUG;
            $pageData = $this->pageService->getPageBySlug($slug);
            
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
}