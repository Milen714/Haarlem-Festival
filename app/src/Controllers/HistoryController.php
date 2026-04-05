<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;
use App\Framework\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use App\Services\Interfaces\IHistoryService;
use App\Services\HistoryService;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Repositories\PageRepository;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Exceptions\ResourceNotFoundException;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private ILogService $logService;
    private IHistoryService $historyService;
    private ITicketService $ticketService;
    private IOrderService $orderService;

    const HISTORY_TOUR_SLUG = 'history-tour';

    public function __construct()
    {
        $this->pageService     = new PageService();
        $this->landmarkService = new LandmarkService();
        $this->logService      = new LogService();
        $this->historyService  = new HistoryService();
        $this->ticketService   = new TicketService();
        $this->orderService    = new OrderService();
    }

    public function index($vars = [])
    {
        try {
            $this->view('History/HistoryHomepage', $this->historyService->getHomepageData());
        } catch (ResourceNotFoundException $e) {
            $this->notFound();
        } catch (\Throwable $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
    }

    public function tour($vars = [])
    {
        try {
            $this->view('History/HistoryTour', $this->historyService->getTourData());
        } catch (ResourceNotFoundException $e) {
            $this->notFound();
        } catch (\Throwable $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
    }

    public function detail(array $vars): void
    {
        try {
            $data = $this->historyService->getDetailData($vars['slug'] ?? '');
            $data['title'] = $data['landmark']->name . ' - Haarlem History';
            $this->view('History/HistoryDetail', $data);
        } catch (ResourceNotFoundException $e) {
            $this->notFound();
        } catch (\Throwable $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function editTourRoute($vars = []): void
    {
        try {
            $pageData  = $this->pageService->getPageBySlug(self::HISTORY_TOUR_SLUG);
            $tourRoute = null;

            foreach ($pageData->content_sections as $section) {
                if ($section->section_type->value === 'tour_route') {
                    $tourRoute = $section;
                    break;
                }
            }

            $stops = [];
            if ($tourRoute && !empty($tourRoute->content_html)) {
                $decoded = json_decode($tourRoute->content_html, true);
                if (is_array($decoded)) {
                    $stops = array_column($decoded, 'name');
                }
            }

            $this->cmsLayout('Cms/TourRoute/Edit', [
                'title'     => 'Edit Tour Route',
                'tourRoute' => $tourRoute,
                'stops'     => $stops,
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function updateTourRoute($vars = []): void
    {
        try {
            $pageData  = $this->pageService->getPageBySlug(self::HISTORY_TOUR_SLUG);
            $tourRoute = null;

            foreach ($pageData->content_sections as $section) {
                if ($section->section_type->value === 'tour_route') {
                    $tourRoute = $section;
                    break;
                }
            }

            if (!$tourRoute) {
                $_SESSION['error'] = 'Tour route section not found.';
                $this->redirect('/cms/history/tour-route');
                return;
            }

            $rawStops = $_POST['stops'] ?? [];
            $stops    = [];
            foreach ($rawStops as $name) {
                $name = trim($name);
                if ($name !== '') {
                    $stops[] = ['name' => $name];
                }
            }

            $tourRoute->content_html = json_encode($stops, JSON_UNESCAPED_UNICODE);

            $pageRepository = new PageRepository();
            $pageRepository->updatePageSectionById($tourRoute);

            $_SESSION['success'] = 'Tour route updated successfully.';
            $this->redirect('/cms/history/tour-route');
        } catch (\Throwable $e) {
            $this->logService->exception('History', $e);
            $_SESSION['error'] = 'Failed to update tour route.';
            $this->redirect('/cms/history/tour-route');
        }
    }
}
