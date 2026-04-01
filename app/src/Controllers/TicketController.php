<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\TicketRepository;
use App\Repositories\VenueRepository;
use App\Services\ArtistService;
use App\Services\LandmarkService;
use App\Services\MediaService;
use App\Services\RestaurantService;
use App\Services\ScheduleService;
use App\Services\TicketService;
use App\Services\VenueService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use App\Exceptions\UserFacingException;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;

class TicketController extends BaseController
{
    private TicketService $ticketService;
    private ScheduleService $scheduleService;
    private ILogService $logService;

    public function __construct()
    {
        $mediaService = new MediaService();
        $venueService = new VenueService();
        $artistService = new ArtistService();
        $restaurantService = new RestaurantService();
        $landmarkService = new LandmarkService();

        $this->ticketService = new TicketService(new TicketRepository());
        $this->scheduleService = new ScheduleService();
        $this->logService = new LogService();
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        $scheduleId = (int)($vars['scheduleId'] ?? 0);

        try {
            $schedule = $this->getScheduleOrThrow($scheduleId);

            $this->cmsLayout('Cms/Schedules/Tickets/Index', [
                'title' => 'Manage Schedule Tickets',
                'schedule' => $schedule,
                'ticketTypes' => $this->ticketService->getTicketTypesByScheduleId($scheduleId),
            ]);
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Not found: ' . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules');
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load tickets.';
            $this->redirect('/cms/schedules');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $scheduleId = (int)($vars['scheduleId'] ?? 0);

        try {
            $schedule = $this->getScheduleOrThrow($scheduleId);

            $this->cmsLayout('Cms/Schedules/Tickets/Form', [
                'title' => 'Create Ticket Type',
                'schedule' => $schedule,
                'ticketType' => null,
                'ticketSchemes' => $this->ticketService->getAllTicketSchemes(),
                'action' => "/cms/schedules/{$scheduleId}/tickets/store",
            ]);
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Not found: ' . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/schedules');
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load form.';
            $this->redirect('/cms/schedules');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        $this->startSession();
        $scheduleId = (int)($vars['scheduleId'] ?? 0);

        try {
            $this->getScheduleOrThrow($scheduleId);
            $ticketType = $this->ticketService->createFromRequest($scheduleId, $_POST);

            $_SESSION['success'] = 'Ticket type #' . ($ticketType->ticket_type_id ?? '') . ' created successfully!';
            $this->redirect("/cms/schedules/{$scheduleId}/tickets");
        } catch (ValidationException $e) {
            $this->logService->info('Ticket', 'Validation error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/schedules/{$scheduleId}/tickets/create");
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to create ticket type.';
            $this->redirect("/cms/schedules/{$scheduleId}/tickets/create");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $scheduleId = (int)($vars['scheduleId'] ?? 0);
        $ticketTypeId = (int)($vars['ticketTypeId'] ?? 0);

        try {
            $schedule = $this->getScheduleOrThrow($scheduleId);
            $ticketType = $this->getTicketTypeForScheduleOrThrow($scheduleId, $ticketTypeId);

            $this->cmsLayout('Cms/Schedules/Tickets/Form', [
                'title' => 'Edit Ticket Type',
                'schedule' => $schedule,
                'ticketType' => $ticketType,
                'ticketSchemes' => $this->ticketService->getAllTicketSchemes(),
                'action' => "/cms/schedules/{$scheduleId}/tickets/update/{$ticketTypeId}",
            ]);
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Not found: ' . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/schedules/{$scheduleId}/tickets");
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load form.';
            $this->redirect("/cms/schedules/{$scheduleId}/tickets");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $this->startSession();
        $scheduleId = (int)($vars['scheduleId'] ?? 0);
        $ticketTypeId = (int)($vars['ticketTypeId'] ?? 0);

        try {
            $ticketType = $this->ticketService->updateFromRequest($ticketTypeId, $scheduleId, $_POST);

            $_SESSION['success'] = 'Ticket type #' . ($ticketType->ticket_type_id ?? $ticketTypeId) . ' updated successfully!';
            $this->redirect("/cms/schedules/{$scheduleId}/tickets");
        } catch (ValidationException | ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/schedules/{$scheduleId}/tickets/edit/{$ticketTypeId}");
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to update ticket type.';
            $this->redirect("/cms/schedules/{$scheduleId}/tickets/edit/{$ticketTypeId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $this->startSession();
        $scheduleId = (int)($vars['scheduleId'] ?? 0);
        $ticketTypeId = (int)($vars['ticketTypeId'] ?? 0);

        try {
            $this->getTicketTypeForScheduleOrThrow($scheduleId, $ticketTypeId);
            $this->ticketService->delete($ticketTypeId);
            $_SESSION['success'] = 'Ticket type deleted successfully!';
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Not found: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to delete ticket type.';
        }

        $this->redirect("/cms/schedules/{$scheduleId}/tickets");
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeIndex($vars = []): void
    {
        try {
            $this->cmsLayout('Cms/TicketSchemes/Index', [
                'title' => 'Manage Ticket Schemes',
                'ticketSchemes' => $this->ticketService->getAllTicketSchemes(),
                'usageCounts' => $this->ticketService->getTicketSchemeUsageCounts(),
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $this->cmsLayout('Cms/TicketSchemes/Index', [
                'title' => 'Manage Ticket Schemes',
                'ticketSchemes' => [],
                'usageCounts' => [],
                'error' => 'Failed to load ticket schemes.',
            ]);
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeCreate($vars = []): void
    {
        $this->cmsLayout('Cms/TicketSchemes/Form', [
            'title' => 'Create Ticket Scheme',
            'ticketScheme' => null,
            'action' => '/cms/ticket-schemes/store',
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeStore($vars = []): void
    {
        $this->startSession();

        try {
            $ticketScheme = $this->ticketService->createTicketSchemeFromRequest($_POST);
            $_SESSION['success'] = "Ticket scheme '{$ticketScheme->name}' created successfully!";
            $this->redirect('/cms/ticket-schemes');
        } catch (ValidationException $e) {
            $this->logService->info('Ticket', 'Validation error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/ticket-schemes/create');
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to create ticket scheme.';
            $this->redirect('/cms/ticket-schemes/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeEdit($vars = []): void
    {
        $ticketSchemeId = (int)($vars['id'] ?? 0);

        try {
            $ticketScheme = $this->ticketService->getTicketSchemeById($ticketSchemeId);

            if (!$ticketScheme) {
                throw new ResourceNotFoundException('Ticket scheme not found.');
            }

            $this->cmsLayout('Cms/TicketSchemes/Form', [
                'title' => 'Edit Ticket Scheme',
                'ticketScheme' => $ticketScheme,
                'action' => "/cms/ticket-schemes/update/{$ticketSchemeId}",
            ]);
        } catch (ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Not found: ' . $e->getMessage());
            $this->startSession();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/ticket-schemes');
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $this->startSession();
            $_SESSION['error'] = 'Failed to load form.';
            $this->redirect('/cms/ticket-schemes');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeUpdate($vars = []): void
    {
        $this->startSession();
        $ticketSchemeId = (int)($vars['id'] ?? 0);

        try {
            $ticketScheme = $this->ticketService->updateTicketSchemeFromRequest($ticketSchemeId, $_POST);
            $_SESSION['success'] = "Ticket scheme '{$ticketScheme->name}' updated successfully!";
            $this->redirect('/cms/ticket-schemes');
        } catch (ValidationException | ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/ticket-schemes/edit/{$ticketSchemeId}");
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to update ticket scheme.';
            $this->redirect("/cms/ticket-schemes/edit/{$ticketSchemeId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function schemeDelete($vars = []): void
    {
        $this->startSession();
        $ticketSchemeId = (int)($vars['id'] ?? 0);

        try {
            $ticketScheme = $this->ticketService->getTicketSchemeById($ticketSchemeId);

            if (!$ticketScheme) {
                throw new ResourceNotFoundException('Ticket scheme not found.');
            }

            $this->ticketService->deleteTicketSchemeSafely($ticketSchemeId);
            $_SESSION['success'] = "Ticket scheme '{$ticketScheme->name}' deleted successfully!";
        } catch (ValidationException | ResourceNotFoundException $e) {
            $this->logService->info('Ticket', 'Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            $this->logService->exception('Ticket', $e);
            $_SESSION['error'] = 'Failed to delete ticket scheme.';
        }

        $this->redirect('/cms/ticket-schemes');
    }

    private function getScheduleOrThrow(int $scheduleId)
    {
        $schedule = $this->scheduleService->getScheduleById($scheduleId);

        if (!$schedule) {
            throw new \Exception('Schedule not found');
        }

        return $schedule;
    }

    private function getTicketTypeForScheduleOrThrow(int $scheduleId, int $ticketTypeId)
    {
        $ticketType = $this->ticketService->getTicketTypeById($ticketTypeId);

        if (!$ticketType) {
            throw new \Exception('Ticket type not found');
        }

        if (($ticketType->schedule?->schedule_id ?? null) !== $scheduleId) {
            throw new \Exception('Ticket type does not belong to this schedule');
        }

        return $ticketType;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}