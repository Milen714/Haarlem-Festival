<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Repositories\PageRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Services\MediaService;
use App\Services\VenueService;
use App\Repositories\VenueRepository;
use App\Services\ScheduleService;
use App\Repositories\ScheduleRepository;
use App\ViewModels\Dance\LineupViewModel;

class DanceController extends BaseController
{
    private PageService $pageService;
    private ArtistService $artistService;
    private VenueService $venueService;
    private MediaService $mediaService;
    private ScheduleService $scheduleService;
    
    // Dance event ID constant
    private const DANCE_EVENT_ID = 4;

    public function __construct()
    {   // Media Service
        $mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($mediaRepository);
        
        // Page Service
        $pageRepository = new PageRepository();
        $this->pageService = new PageService($pageRepository);
        
        // Artist Service
        $artistRepository = new ArtistRepository();
        $this->artistService = new ArtistService($artistRepository, $this->mediaService);
        
        // Venue Service
        $venueRepository = new VenueRepository();
        $this->venueService = new VenueService($venueRepository, $this->mediaService);

        // Schedule Service
        $scheduleRepository = new ScheduleRepository();
        $this->scheduleService = new ScheduleService($scheduleRepository);
    }

    public function index($vars = [])
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = ltrim($uri, '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);

            $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);

            $venues = [];
            $sections = $pageData->content_sections ?? [];
            
            $organizedSections = $this->organizeHomeSections($sections);
            
            $this->view('Dance/index', [
                'title' => $pageData->title ?? 'Dance Event',
                'pageData' => $pageData,
                'sections' => $sections,
                'heroSection' => $organizedSections['heroSection'],
                'artistSection' => $organizedSections['artistSection'],
                'specialSection' => $organizedSections['specialSection'],
                'venueSection' => $organizedSections['venueSection'],
                'ticketSection' => $organizedSections['ticketSection'],
                'gallerySection' => $organizedSections['gallerySection'],
                'artists' => $artists,
                'venues' => $venues
            ]);
        } catch (\Exception $e) {
            error_log("Error in DanceController index method: " . $e->getMessage());
            $this->notFound();
        }
    }

    public function lineUp()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = ltrim($uri, '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);

            $headLinerSection = array_filter($pageData->content_sections ?? [], function($section) {
                return stripos($section->title, 'The 2025 Headliners') !== false;
                });
            $headLinerSection = array_shift($headLinerSection);
            $schedulesSection = $this->scheduleService->getSchedulesByEventId(self::DANCE_EVENT_ID);
            $viewModel = new LineupViewModel($pageData, $artists, $headLinerSection, $schedulesSection);
            $this->view('Dance/lineup', [
                'title' => 'Dance Lineup',
                'vm' => $viewModel
            ]);
        } catch (\Exception $e) {
            error_log("Error in DanceController lineUp method: " . $e->getMessage());
            $this->notFound();
        }
    }

    private function organizeHomeSections(array $sections): array
    {
        $organized = [
            'heroSection' => null,
            'artistSection' => null,
            'specialSection' => null,
            'venueSection' => null,
            'ticketSection' => null,
            'gallerySection' => null,
        ];
        
        foreach ($sections as $section) {
            switch ($section->section_type->value) {
                case 'hero_picture':
                case 'hero_gallery':
                    $organized['heroSection'] = $section;
                    break;
                case 'article':
                    if (stripos($section->title, 'Dutch Dance Legends') !== false) {
                        $organized['artistSection'] = $section;
                    } elseif (stripos($section->title, 'Back2Back Specials') !== false) {
                        $organized['specialSection'] = $section;
                    } elseif (stripos($section->title, 'The City is Your Dancefloor') !== false) {
                        $organized['venueSection'] = $section;
                    } elseif (stripos($section->title, 'All-Access Experience') !== false) {
                        $organized['ticketSection'] = $section;
                    }  elseif (stripos($section->title, 'Gallery') !== false) {
                        $organized['gallerySection'] = $section;
                    } 
                    break;
            }
        }
        return $organized;
    }

}