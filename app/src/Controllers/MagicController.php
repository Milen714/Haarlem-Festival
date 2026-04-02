<?php
namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\PageService;
use App\Services\Interfaces\IPageService;
use App\ViewModels\Magic\MagicAccessibility;
use App\ViewModels\Magic\MagicTicketsViewModel;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\ScheduleService;
use App\Models\Enums\EventType;
use Stripe\Event;
use App\Exceptions\UserFacingException;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;

class MagicController extends BaseController
{
    private IPageService $pageService;
    private IScheduleService $scheduleService;
    private ITicketService $ticketService;
    private ILogService $logService;

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService();
        $this->logService = new LogService();
    }

    /**
     * Display the magic landing page with accessibility and event information.
     */
    public function index($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $pageModel = new MagicAccessibility($pageData);
            $this->View('Magic/MagicLandingRef', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (UserFacingException $e) {
            $this->logService->info('Magic', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the page.');
        } catch (\Throwable $e) {
            $this->logService->exception('Magic', $e);
            $this->internalServerError('An error occurred while loading the page.');
        }
    }

    /**
     * Display the magic accessibility information page.
     */
    public function accessibility($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicAccessibility', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (UserFacingException $e) {
            $this->logService->info('Magic', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the page.');
        } catch (\Throwable $e) {
            $this->logService->exception('Magic', $e);
            $this->internalServerError('An error occurred while loading the page.');
        }
    }
    /**
     * Display the Lorentz formula explanation page.
     */
    public function lorentzFormula($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicLorentz', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (UserFacingException $e) {
            $this->logService->info('Magic', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the page.');
        } catch (\Throwable $e) {
            $this->logService->exception('Magic', $e);
            $this->internalServerError('An error occurred while loading the page.');
        }
    }
    /**
     * Display the magic ticket selection page filtered by date.
     * Accepts optional 'date' query parameter for filtering schedules.
     */
    public function magicTicketSelect($vars = []): void
    {
         $slug = ltrim($_SERVER['REQUEST_URI'], '/');
         $date = $_GET['date'] ?? null;
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            $schedules = $this->scheduleService->getAllSchedules(EventType::Magic->value, $date);
            $pageModel = new MagicAccessibility($pageData);
            $pageModel->ticketsViewModel = new MagicTicketsViewModel(schedulesByDate: $schedules);
            
            $this->view('Magic/MagicTicketSelect', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (UserFacingException $e) {
            $this->logService->info('Magic', 'User-facing error: ' . $e->getMessage());
            $this->internalServerError('An error occurred while loading the page.');
        } catch (\Throwable $e) {
            $this->logService->exception('Magic', $e);
            $this->internalServerError('An error occurred while loading the page.');
        }
    }
    /**
     * Returns available ticket types for a specific schedule as JSON.
     * Requires 'schedule_id' query parameter.
     */
    public function magicGetTicketTypes($vars = []): void
    {
        $scheduleId = $_GET['schedule_id'] ?? null;
        if (!$scheduleId) {
            $this->sendSuccessResponse(['success' => false, 'message' => 'Missing schedule_id parameter'], 400);
            return;
        }
        try{
            $ticketTypes = $this->ticketService->getTicketTypesByScheduleId((int)$scheduleId); 
            if (empty($ticketTypes)) {
                $this->sendSuccessResponse(['success' => false, 'message' => 'No ticket types found for this schedule'], 404);
                return;
            }
            $this->sendSuccessResponse(['success' => true, 'data' => $ticketTypes]);

        } catch (UserFacingException $e) {
            $this->logService->info('Magic', 'User-facing error: ' . $e->getMessage());
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading ticket types.'], 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Magic', $e);
            $this->sendSuccessResponse(['success' => false, 'message' => 'An error occurred while loading ticket types.'], 500);
        }
    }

    
}