<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;
use App\Services\LogService;
use App\Services\TicketService;
use App\Services\Interfaces\ILogService;

class JazzController extends BaseController
{
    private JazzService $jazzService;
    private TicketService $ticketService;
    private ILogService $logService;

    /**
     * Wires up JazzService (which handles all Jazz page data assembly) and TicketService
     * (used directly by getTicketTypes for the AJAX ticket-type endpoint).
     */
    public function __construct()
    {
        $this->jazzService = new JazzService();
        $this->ticketService = new TicketService();
        $this->logService = new LogService();
    }

    /**
     * Renders the main Jazz overview page with artists, venues, schedule, and ticket types.
     * Delegates all data assembly to JazzService and maps known exceptions to HTTP responses.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    public function index($vars = [])
    {
        try {
            $this->view('Jazz/index', $this->jazzService->loadJazzOverview());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        } catch (\Throwable $e) {

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        }
    }

    /**
     * AJAX endpoint that returns the ticket types for a given schedule slot as JSON.
     * Reads schedule_id from the query string and responds with the ticket types array,
     * or a 400/404/500 JSON error if the parameter is missing, no types are found, or a failure occurs.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    public function getTicketTypes($vars = []): void
    {
        $scheduleId = $_GET['schedule_id'] ?? null;
        if (!$scheduleId) {
            $this->jsonResponse(['success' => false, 'message' => 'Missing schedule_id parameter'], 400);
            return;
        }
        try {
            $ticketTypes = $this->ticketService->getTicketTypesByScheduleId((int)$scheduleId);
            if (empty($ticketTypes)) {
                $this->jsonResponse(['success' => false, 'message' => 'No ticket types found for this schedule'], 404);
                return;
            }
            $this->jsonResponse(['success' => true, 'data' => $ticketTypes]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Renders the Jazz schedule page, which shows the full timetable grouped by date.
     * Maps known exceptions to appropriate HTTP responses.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    public function schedule($vars = []): void
    {
        try {
            $this->view('Jazz/schedule', $this->jazzService->loadJazzSchedule());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        } catch (\Throwable $e) {

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        }
    }
}
