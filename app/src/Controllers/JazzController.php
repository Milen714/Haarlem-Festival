<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;
use App\Services\TicketService;

class JazzController extends BaseController
{
    private JazzService $jazzService;
    private TicketService $ticketService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
        $this->ticketService = new TicketService();
    }

    public function index($vars = [])
    {
        try {
            $this->view('Jazz/index', $this->jazzService->loadJazzOverview());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            error_log("Jazz page configuration error: " . $e->getMessage());
            $this->internalServerError();
        } catch (\Throwable $e) {

            error_log("Jazz page error: " . $e->getMessage());
            $this->internalServerError();
        }
    }

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

    public function schedule($vars = []): void
    {
        try {
            $this->view('Jazz/schedule', $this->jazzService->loadJazzSchedule());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            error_log("Jazz schedule configuration error: " . $e->getMessage());
            $this->internalServerError();
        } catch (\Throwable $e) {

            error_log("Jazz schedule page error: " . $e->getMessage());
            $this->internalServerError();
        }
    }
}
