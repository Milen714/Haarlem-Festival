<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\Interfaces\ITicketRepository;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Services\VenueService;
use App\Services\ScheduleService;
use App\Services\TicketService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ITicketService;
use App\ViewModels\Dance\LineupViewModel;
use App\ViewModels\Dance\VenueViewModel;


class DanceController extends BaseController
{
    private IPageService $pageService;
    private IArtistService $artistService;
    private IVenueService $venueService;
    private IMediaService $mediaService;
    private IScheduleService $scheduleService;
    private ITicketService $ticketService;
    
    // Dance event ID constant
    private const DANCE_EVENT_ID = 4;

    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->pageService = new PageService();
        $this->artistService = new ArtistService();
        $this->venueService = new VenueService();
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService();
    }

    public function index($vars = [])
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = ltrim($uri, '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);

            $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);

            $backtoback = $this->scheduleService->getBackToBackSpecialsByEventId(self::DANCE_EVENT_ID);
            $ticketLookup = $this->getTicketLookupForSchedules($backtoback);

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
                'backtoback' => $backtoback,
                'ticketLookup' => $ticketLookup
            ]);
        } catch (\Exception $e) {
            error_log("Error in DanceController index method: " . $e->getMessage());
            $this->notFound();
        }
    }

    public function lineUp()
    {
        $slug = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);
            $schedulesSection = $this->scheduleService->getSchedulesByEventId(self::DANCE_EVENT_ID);

            $headLinerSection = array_filter($pageData->content_sections ?? [], function($section) {
                return stripos($section->title, 'The 2025 Headliners') !== false;
                });
            $headLinerSection = array_shift($headLinerSection);
            $ticketLookup = $this->getTicketLookupForSchedules($schedulesSection);

            $viewModel = new LineupViewModel($pageData, $artists, $headLinerSection, $schedulesSection);
            
            $this->view('Dance/lineup', [
                'title' => 'Dance Lineup',
                'vm' => $viewModel,
                'ticketLookup' => $ticketLookup
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->notFound();
        }
    }

    public function venues()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = ltrim($uri, '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $venues = $this->venueService->getVenuesByEventId(self::DANCE_EVENT_ID);
            $viewModel = new VenueViewModel($pageData, $venues);
            $this->view('Dance/venues', [
                'title' => 'Dance Venues',
                'vm' => $viewModel
            ]);
        } catch (\Exception $e) {
            error_log("Error in DanceController venues method: " . $e->getMessage());
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

    private function getTicketLookupForSchedules(array $schedules): array
    {
        $lookup = [];
        foreach ($schedules as $schedule) {
            $ticket = $this->ticketService->getTicketTypesByScheduleId($schedule->schedule_id)[0] ?? null;
            if ($ticket) {
                $lookup[$schedule->schedule_id] = [
                    'id' => $ticket->ticket_type_id,
                    'price' => $ticket->ticket_scheme->price ?? 0.0,
                    'available' => ($ticket->capacity - $ticket->tickets_sold)
                ];
            }
        }
        return $lookup;
    }

}