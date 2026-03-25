<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
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
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class ScheduleController extends BaseController
{
    private ScheduleService $scheduleService;
    private TicketService $ticketService;

    public function __construct()
    {
        $this->scheduleService = new ScheduleService();

        $this->ticketService = new TicketService(new TicketRepository());
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        $this->startSession();

        if (isset($_GET['clear'])) {
            unset($_SESSION['schedule_filters']);
            $this->redirect('/cms/schedules');
            return;
        }

        try {

            if (array_key_exists('event_type', $_GET) || array_key_exists('date', $_GET)) {
                $eventType = trim((string)($_GET['event_type'] ?? '')) ?: null;
                $date      = trim((string)($_GET['date'] ?? '')) ?: null;
                $_SESSION['schedule_filters'] = ['event_type' => $eventType, 'date' => $date];
            } else {
                $saved     = $_SESSION['schedule_filters'] ?? [];
                $eventType = $saved['event_type'] ?? null;
                $date      = $saved['date'] ?? null;
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
        } catch (\Throwable $e) {
            error_log("Schedule list error: " . $e->getMessage());
            $this->cmsLayout('Cms/Schedules/Index', [
                'title'          => 'Manage Schedules',
                'schedules'      => [],
                'eventCategories' => [],
                'filterType'     => null,
                'filterDate'     => null,
                'error'          => 'Failed to load schedules.',
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
        } catch (\Throwable $e) {
            error_log("Schedule create form error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = 'Failed to load schedule form.';
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
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules/create');
        } catch (\Throwable $e) {
            error_log("Schedule store error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create schedule.';
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
                throw new ResourceNotFoundException('Schedule not found.');
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
        } catch (ResourceNotFoundException $e) {
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules');
        } catch (\Throwable $e) {
            error_log("Schedule edit error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = 'Failed to load schedule.';
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
        } catch (ValidationException | ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/schedules/edit/{$scheduleId}");
        } catch (\Throwable $e) {
            error_log("Schedule update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update schedule.';
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
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            error_log("Schedule delete error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete schedule.';
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