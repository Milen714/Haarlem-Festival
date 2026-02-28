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
    private VenueService $venueService;
    private RestaurantService $restaurantService;

    private const YUMMY_EVENT_ID = 1;
    private const YUMMY_SLUG = 'events-yummy';

    
    public function __construct()
    {
        $mediaRepository = new MediaRepository();
        $mediaService = new MediaService($mediaRepository);
        $pageRepository = new PageRepository();
        $this->pageService = new PageService($pageRepository);
        $venueRepository = new VenueRepository();
        $this->venueService = new VenueService($venueRepository, $mediaService);
        $restaurantRepository = new RestaurantRepository();
        $this->restaurantService = new RestaurantService($restaurantRepository);

    }
    public function index()
    {
            $slug = self::YUMMY_SLUG;
                
            $pageData = $this->pageService->getPageBySlug($slug);
            
            if (!$pageData) {
                error_log("Yummy page data not found for slug: {$slug}");
                $this->notFound();
                return;
            }
            
            $venues = $this->venueService->getVenuesByEventId(self::YUMMY_EVENT_ID);
            $restaurants = $this->restaurantService->getRestaurantsByEventId(self::YUMMY_EVENT_ID);
            
            $this->view('Yummy/index', [
                'title' => $pageData->title ?? 'Yummy Event',
                'pageData' => $pageData,
                'venues' => $venues,
                'section' => $pageData->content_sections ?? [],
                'restaurants' => $restaurants
            ]);
    }
}