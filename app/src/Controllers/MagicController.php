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

class MagicController extends BaseController
{
    private IPageService $pageService;
    private IScheduleService $scheduleService;
    private ITicketService $ticketService;

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService();
    }

    public function index($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                //$this->notFound();
                //return;
                throw new \Exception("We are sorry, but the page you are looking for cannot be found. Please check the URL and try again.");
            }
            $pageModel = new MagicAccessibility($pageData);
            $this->View('Magic/MagicLandingRef', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
    }

    public function accessibility($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                $this->notFound();
                return;
            }
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicAccessibility', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
        
    }
    public function lorentzFormula($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                $this->notFound();
                return;
            }
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicLorentz', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
        
    }
    public function magicTicketSelect($vars = []): void
    {
         $slug = ltrim($_SERVER['REQUEST_URI'], '/');
         $date = $_GET['date'] ?? null;
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                $this->notFound();
                return;
            }
            $schedules = $this->scheduleService->getAllSchedules(EventType::Magic->value, $date);
            $pageModel = new MagicAccessibility($pageData);
            $pageModel->ticketsViewModel = new MagicTicketsViewModel(schedulesByDate: $schedules);
            
            $this->view('Magic/MagicTicketSelect', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
    }
    public function magicGetTicketTypes($vars = []): void{
        $scheduleId = $_GET['schedule_id'] ?? null;
        if (!$scheduleId) {
            $this->jsonResponse(['success' => false, 'message' => 'Missing schedule_id parameter'], 400);
            return;
        }
        try{
            $ticketTypes = $this->ticketService->getTicketTypesByScheduleId((int)$scheduleId); 
            if (empty($ticketTypes)) {
                $this->jsonResponse(['success' => false, 'message' => 'No ticket types found for this schedule'], 404);
                return;
            }
            $this->jsonResponse(['success' => true, 'data' => $ticketTypes]);


        }catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    
}