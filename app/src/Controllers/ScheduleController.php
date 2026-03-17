<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ScheduleService;
use App\Services\VenueService;
use App\Services\ArtistService;
use App\Services\RestaurantService;
use App\Services\LandmarkService;
use App\Services\MediaService;
use App\Services\TicketService;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\MediaRepository;
use App\Repositories\TicketRepository;
use App\Services\Interfaces\IScheduleService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class ScheduleController extends BaseController
{
    private ScheduleService $scheduleService;
    private TicketService $ticketService;

    public function __construct()
    {
        $mediaService = new MediaService(new MediaRepository());

        $venueService      = new VenueService(new VenueRepository(), $mediaService);
        $artistService     = new ArtistService(new ArtistRepository(), $mediaService);
        $restaurantService = new RestaurantService(new RestaurantRepository(), $mediaService);
        $landmarkService   = new LandmarkService();

        $this->scheduleService = new ScheduleService(
            new ScheduleRepository(),
            $venueService,
            $artistService,
            $restaurantService,
            $landmarkService
        );

        $this->ticketService = new TicketService(new TicketRepository());
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        try {
            $eventType = $_GET['event_type'] ?? null;
            $date      = $_GET['date'] ?? null;

            // Sanitizes the inputs
            if (is_string($eventType)) {
                $eventType = trim($eventType) ?: null;
            }
            if (is_string($date)) {
                $date = trim($date) ?: null;
            }

            $schedules   = $this->scheduleService->getAllSchedules($eventType, $date);
            $ids         = array_map(fn($s) => $s->schedule_id, $schedules);
            $grouped     = $this->ticketService->getTicketTypesByScheduleIds($ids);
            foreach ($schedules as $schedule) {
                $schedule->ticketTypes = $grouped[$schedule->schedule_id] ?? [];
            }
            $eventCategories  = $this->scheduleService->getAllEventCategories();

            $this->cmsLayout('Cms/Schedules/Index', [
                'title'          => 'Manage Schedules',
                'schedules'      => $schedules,
                'eventCategories' => $eventCategories,
                'filterType'     => $eventType,
                'filterDate'     => $date,
            ]);
        } catch (\Exception $e) {
            error_log("Schedule list error: " . $e->getMessage());
            $this->cmsLayout('Cms/Schedules/Index', [
                'title'          => 'Manage Schedules',
                'schedules'      => [],
                'eventCategories' => [],
                'filterType'     => null,
                'filterDate'     => null,
                'error'          => 'Failed to load schedules: ' . $e->getMessage(),
            ]);
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        try {
            $this->cmsLayout('Cms/Schedules/Form', [
                'title'          => 'Create New Schedule',
                'schedule'       => null,
                'action'         => '/cms/schedules/store',
                'eventCategories' => $this->scheduleService->getAllEventCategories(),
                'venues'         => $this->scheduleService->getAllVenues(),
                'artists'        => $this->scheduleService->getAllArtists(),
                'restaurants'    => $this->scheduleService->getAllRestaurants(),
                'landmarks'      => $this->scheduleService->getAllLandmarks(),
                'ticketTypes'    => [],
            ]);
        } catch (\Exception $e) {
            error_log("Schedule create form error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        $this->startSession();

        try {
            $schedule = $this->scheduleService->createFromRequest($_POST);
            $_SESSION['success'] = "Schedule for " . ($schedule->date?->format('d M Y') ?? 'N/A') . " created successfully!";
            $this->redirect('/cms/schedules');
        } catch (\Exception $e) {
            error_log("Schedule store error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $scheduleId = (int)($vars['id'] ?? 0);

        try {
            $schedule = $this->scheduleService->getScheduleById($scheduleId);

            if (!$schedule) {
                $this->startSession();
                $_SESSION['error'] = 'Schedule not found';
                $this->redirect('/cms/schedules');
                return;
            }

            $this->cmsLayout('Cms/Schedules/Form', [
                'title'          => 'Edit Schedule #' . $scheduleId,
                'schedule'       => $schedule,
                'action'         => "/cms/schedules/update/{$scheduleId}",
                'eventCategories' => $this->scheduleService->getAllEventCategories(),
                'venues'         => $this->scheduleService->getAllVenues(),
                'artists'        => $this->scheduleService->getAllArtists(),
                'restaurants'    => $this->scheduleService->getAllRestaurants(),
                'landmarks'      => $this->scheduleService->getAllLandmarks(),
                'ticketTypes'    => $this->ticketService->getTicketTypesByScheduleId($scheduleId),
            ]);
        } catch (\Exception $e) {
            error_log("Schedule edit error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = 'Failed to load schedule: ' . $e->getMessage();
            $this->redirect('/cms/schedules');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $this->startSession();
        $scheduleId = (int)($vars['id'] ?? 0);

        try {
            $schedule = $this->scheduleService->updateFromRequest($scheduleId, $_POST);
            $_SESSION['success'] = "Schedule for " . ($schedule->date?->format('d M Y') ?? 'N/A') . " updated successfully!";
            $this->redirect('/cms/schedules');
        } catch (\Exception $e) {
            error_log("Schedule update error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/schedules/edit/{$scheduleId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $this->startSession();
        $scheduleId = (int)($vars['id'] ?? 0);

        try {
            $this->scheduleService->deleteSchedule($scheduleId);
            $_SESSION['success'] = "Schedule deleted successfully!";
        } catch (\Exception $e) {
            error_log("Schedule delete error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/cms/schedules');
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
