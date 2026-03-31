<?php

namespace App\Controllers;

use App\Framework\BaseController;
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
use App\Services\LogService;
use App\Services\Interfaces\ILogService;

class ScheduleController extends BaseController
{
    private ScheduleService $scheduleService;
    private TicketService $ticketService;
    private ILogService $logService;

    /**
     * Wires up ScheduleService for all schedule CRUD and TicketService for attaching
     * ticket type data to schedule rows in the listing and edit views.
     */
    public function __construct()
    {
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService(new TicketRepository());
        $this->logService = new LogService();
    }

    /**
     * Renders the CMS schedule listing page with optional filtering by event type and date.
     * Persists the active filters in the session so they survive a page reload.
     * Visiting with ?clear in the URL resets the filters and redirects.
     * Also fetches ticket types in bulk for all visible schedules to show sold-out status.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
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

    /**
     * Renders the empty schedule create form with all dropdown data pre-loaded.
     * Redirects to the listing with an error if the form data cannot be assembled.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load schedule form.';
            $this->redirect('/cms/schedules');
        }
    }

    /**
     * Handles the schedule create form submission.
     * Passes $_POST to ScheduleService, sets a success flash, and redirects to the listing.
     * On validation or other failure, stores the error in the session and redirects back to the form.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
            $_SESSION['error'] = 'Failed to create schedule.';
            $this->redirect('/cms/schedules/create');
        }
    }

    /**
     * Renders the schedule edit form pre-populated with the current schedule data and all dropdowns.
     * Redirects to the listing with an error if the schedule does not exist.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the schedule's primary key.
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load schedule.';
            $this->redirect('/cms/schedules');
        }
    }

    /**
     * Handles the schedule update form submission.
     * Delegates to ScheduleService, then redirects to the listing on success
     * or back to the edit form with a session error on failure.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the schedule's primary key.
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
            $_SESSION['error'] = 'Failed to update schedule.';
            $this->redirect("/cms/schedules/edit/{$scheduleId}");
        }
    }

    /**
     * Handles the schedule delete action.
     * Delegates to ScheduleService and sets a flash message for the result.
     * Always redirects to the schedule listing regardless of outcome.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the schedule's primary key.
     *
     * @return void
     */
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
            $this->logService->exception('Schedule', $e);
            $_SESSION['error'] = 'Failed to delete schedule.';
        }

        $this->redirect('/cms/schedules');
    }

    /**
     * Ensures the PHP session is started before writing to $_SESSION.
     * Safe to call multiple times — checks session_status() before calling session_start().
     *
     * @return void
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
