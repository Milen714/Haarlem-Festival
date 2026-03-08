<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Services\VenueService;
use App\Services\MediaService;
use App\Services\ScheduleService;
use App\Services\RestaurantService;
use App\Services\LandmarkService;
use App\Repositories\PageRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\VenueRepository;
use App\Repositories\MediaRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\LandmarkRepository;

class JazzController extends BaseController
{
    private PageService $pageService;
    private ArtistService $artistService;
    private VenueService $venueService;
    private ScheduleService $scheduleService;

    private const JAZZ_EVENT_ID = 3;
    private const JAZZ_SLUG = 'events-jazz';  
    
    public function __construct()
    {
        // Create MediaService once and reuse it
        $mediaRepository = new MediaRepository();
        $mediaService = new MediaService($mediaRepository);
        
        // Page Service
        $pageRepository = new PageRepository();
        $this->pageService = new PageService($pageRepository);
        
        // Artist Service
        $artistRepository = new ArtistRepository();
        $this->artistService = new ArtistService($artistRepository, $mediaService);
        
        // Venue Service (reusing the same $mediaService)
        $venueRepository = new VenueRepository();
        $this->venueService = new VenueService($venueRepository, $mediaService);

        // Schedule Service
        $this->scheduleService = new ScheduleService(
            new ScheduleRepository(),
            $this->venueService,
            $this->artistService,
            new RestaurantService(new RestaurantRepository()),
            new LandmarkService(new LandmarkRepository())
        );
    }

    public function index($vars = [])
    {
        try {
            $slug = self::JAZZ_SLUG;
            
            $pageData = $this->pageService->getPageBySlug($slug);
            
            if (!$pageData) {
                error_log("Jazz page data not found for slug: {$slug}");
                $this->notFound();
                return;
            }
            
            $artists = $this->artistService->getArtistsByEventId($pageData->event_category->event_id);
            $venues = $this->venueService->getVenuesByEventId($pageData->event_category->event_id);
            
            $this->view('Jazz/index', [
                'title' => $pageData->title ?? 'Jazz Event',
                'pageData' => $pageData,
                'sections' => $pageData->content_sections ?? [],
                'artists' => $artists,
                'venues' => $venues,
                'scheduleByDate' => [],
            ]);
            
        } catch (\Exception $e) {
            error_log("Jazz page error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->internalServerError();
        }
    }

    public function schedule($vars = []): void
    {
        try {
            $schedules = $this->scheduleService->getSchedulesByEventId(self::JAZZ_EVENT_ID);

            // Group Schedule objects by date
            $scheduleByDate = [];
            foreach ($schedules as $schedule) {
                $dateKey = $schedule->date ? $schedule->date->format('Y-m-d') : 'unknown';
                $scheduleByDate[$dateKey][] = $schedule;
            }
            ksort($scheduleByDate);

            $this->view('Jazz/schedule', [
                'title'          => 'Jazz Festival Schedule',
                'scheduleByDate' => $scheduleByDate,
            ]);
        } catch (\Exception $e) {
            error_log("Jazz schedule page error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->internalServerError();
        }
    }
}