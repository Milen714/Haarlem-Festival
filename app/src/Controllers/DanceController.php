<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Services\VenueService;
use App\Services\ScheduleService;
use App\Services\TicketService;
use App\Services\DanceService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ITicketService;
use App\Services\Interfaces\IDanceService;
use App\ViewModels\Dance\LineupViewModel;
use App\ViewModels\Dance\VenueViewModel;
use App\ViewModels\Dance\DanceIndexViewModel;
use App\Exceptions\DanceEventNotFoundException;
use App\Exceptions\ArtistNotFoundException;
use App\Exceptions\VenueNotFoundException;
use App\Exceptions\ScheduleNotFoundException;
use App\Exceptions\ApplicationException;

class DanceController extends BaseController
{
    // Dance event ID constant
    private const DANCE_EVENT_ID = 4;
    private IPageService $pageService;
    private IArtistService $artistService;
    private IVenueService $venueService;
    private IMediaService $mediaService;
    private IScheduleService $scheduleService;
    private ITicketService $ticketService;
    private IDanceService $danceService;

    public function __construct()
    {
        $this->venueService = new VenueService();
        $this->mediaService = new MediaService();
        $this->pageService = new PageService();
        $this->artistService = new ArtistService();
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService();

        $this->danceService = new DanceService(
            $this->ticketService,
            $this->scheduleService,
            $this->artistService,
            $this->pageService
        );
    }
    public function index(): void
    {
        $slug = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        
        try {
            $data = $this->danceService->getDanceOverviewData($slug, self::DANCE_EVENT_ID);
            
            if (!$data || !isset($data['pageData'])) {
                throw new DanceEventNotFoundException('Dance event page not found');
            }

            $organized = $this->organizeHomeSections($data['pageData']->content_sections ?? []);

            $viewModel = new DanceIndexViewModel(
                $data['pageData'],
                $organized['heroSection'],
                $organized['artistSection'],
                $organized['specialSection'],
                $organized['venueSection'],
                $organized['ticketSection'],
                $organized['gallerySection'],
                $data['pageData']->title ?? 'Dance Event'
            );

            $this->view('Dance/index', [
                'title' => $viewModel->title,
                'vm' => $viewModel,
                'venueSection' => $organized['venueSection'],
                'heroSection' => $organized['heroSection'],
                'artistSection' => $organized['artistSection'],
                'specialSection' => $organized['specialSection'],
                'ticketSection' => $organized['ticketSection'],
                'gallerySection' => $organized['gallerySection']
            ] + $data);

        } catch (DanceEventNotFoundException $e) {
            error_log("Dance Index Error: " . $e->getMessage());
            $this->notFound();
        } catch (\Exception $e) {
            error_log("Dance Index Error: " . $e->getMessage());
            throw new ApplicationException('Failed to load dance event page', 0, $e);
        }
    }
    public function lineUp(): void
    {
        $slug = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            
            if (!$pageData) {
                throw new DanceEventNotFoundException('Dance lineup page not found');
            }
            
            $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);
            
            if (empty($artists)) {
                throw new ArtistNotFoundException('No artists found for dance event');
            }
            
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
        } catch (DanceEventNotFoundException | ArtistNotFoundException $e) {
            error_log("Dance Lineup Error: " . $e->getMessage());
            $this->notFound();
        } catch (\Exception $e) {
            error_log("Dance Lineup Error: " . $e->getMessage());
            throw new ApplicationException('Failed to load dance lineup page', 0, $e);
        }
    }
    public function venues(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = ltrim($uri, '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            
            if (!$pageData) {
                throw new DanceEventNotFoundException('Dance venues page not found');
            }
            
            $venues = $this->venueService->getVenuesByEventId(self::DANCE_EVENT_ID);
            
            if (empty($venues)) {
                throw new VenueNotFoundException('No venues found for dance event');
            }
            
            $viewModel = new VenueViewModel($pageData, $venues);
            $this->view('Dance/venues', [
                'title' => 'Dance Venues',
                'vm' => $viewModel
            ]);
        } catch (DanceEventNotFoundException | VenueNotFoundException $e) {
            error_log("Dance Venues Error: " . $e->getMessage());
            $this->notFound();
        } catch (\Exception $e) {
            error_log("Error in DanceController venues method: " . $e->getMessage());
            throw new ApplicationException('Failed to load dance venues page', 0, $e);
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