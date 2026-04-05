<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;
use App\Framework\BaseController;
use App\Models\Enums\TicketSchemeEnum;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use App\Services\Interfaces\IHistoryService;
use App\Services\HistoryService;
use App\ViewModels\History\TicketHistoryViewModel;
use App\Models\History\TicketSelectionDTO;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Models\Payment\OrderItem;
use App\Repositories\PageRepository;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private ILogService $logService;
    private IHistoryService $historyService;
    private ITicketService $ticketService;
    private IOrderService $orderService;

    const HISTORY_SLUG = 'events-history';

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->landmarkService = new LandmarkService();
        $this->logService = new LogService();
        $this->historyService = new HistoryService();
        $this->ticketService = new TicketService();
        $this->orderService = new OrderService();
    }

    public function index($vars = [])
    {
        try {
            $pageData = $this->pageService->getPageBySlug(self::HISTORY_SLUG);
            
            if (!$pageData) {
                $this->notFound();
                return;
            }

            $title = $pageData->title; 

            $sections = $pageData->content_sections ?? [];
            $hero = null;
            $welcome = null;
            $bookTour = null;

            foreach ($sections as $s) {
                $type = $s->section_type->value;
                if ($type === 'welcome') {
                    $welcome = $s;
                } elseif ($type === 'book_tour') {
                    $bookTour = $s;
                }
                elseif ($type === 'hero_picture') { 
                    $hero = $s;
                }
            }

            $landmarks = $this->landmarkService->getFeaturedLandmarks();

            $this->view('History/HistoryHomepage', [
                'pageData'  => $pageData,
                'hero'      => $hero,
                'welcome'   => $welcome,
                'landmarks' => $landmarks,
                'bookTour'  => $bookTour
            ]);

        } catch (\Exception $e) {
            $this->logService->exception('History', $e);
        }
    }

    const HISTORY_TOUR_SLUG = 'history-tour'; 

    public function tour($vars = [])
    {
        try {
            $pageData = $this->pageService->getPageBySlug(self::HISTORY_TOUR_SLUG);
            
            if (!$pageData) {
                $this->notFound();
                return;
            }

            $ticketOptions = $this->getTicketOptions();

            $sections = $pageData->content_sections ?? [];
            $hero = null;
            $tourInfo = null;
            $bookTour = null;
            $tourFeatures = [];
            $goodToKnow = null;
            $tourRoute = null;

            foreach ($sections as $s) {
                $type = $s->section_type->value;

                if ($type === 'tour_info') {
                    $tourInfo = $s;
                } elseif ($type === 'tour_features') {
                    $tourFeatures[] = $s;
                } elseif ($type === 'good_to_know') {
                    $goodToKnow = $s;
                } elseif ($type === 'hero_picture') {
                    $hero = $s;
                } elseif ($type === 'book_tour') {
                    $bookTour = $s;
                } elseif ($type === 'tour_route') {
                    $tourRoute = $s;
                }
            }

            $this->view('History/HistoryTour', [
                'pageData'        => $pageData,
                'hero'            => $hero,
                'tourInfo'        => $tourInfo,
                'bookTour'        => $bookTour,
                'ticketOptions'   => $ticketOptions,
                'tourFeatures'    => $tourFeatures,
                'goodToKnow'      => $goodToKnow,
                'tourRoute'       => $tourRoute
            ]);

        } catch (\Exception $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
    }

    private function getTicketOptions(): TicketHistoryViewModel {
        return $this->historyService->getAvailableTourOptions();   

    }

    const HISTORY_DETAIL_SLUG = 'detail'; 

    /** @param array $vars */
    public function detail(array $vars): void
{
    $slug = $vars['slug'] ?? '';
    
    $landmark = $this->landmarkService->getLandmarkBySlug($slug);

    if (!$landmark) {
        $this->notFound(); 
        return;
    }

    $introImage = '/Assets/Home/ImagePlaceholder.png';
    $historyImage = '/Assets/Home/ImagePlaceholder.png';
    $whyVisitImage = '/Assets/Home/ImagePlaceholder.png';

    if (!empty($landmark->gallery) && !empty($landmark->gallery->media_items)) {
        $items = array_values($landmark->gallery->media_items);

        if (isset($items[0]) && !empty($items[0]->media)) {
            $introImage = '/' . ltrim($items[0]->media->file_path, '/');
        }
        if (isset($items[1]) && !empty($items[1]->media)) {
            $historyImage = '/' . ltrim($items[1]->media->file_path, '/');
        }
        if (isset($items[2]) && !empty($items[2]->media)) {
            $whyVisitImage = '/' . ltrim($items[2]->media->file_path, '/');
        }
    }

    $otherLandmarks = [];
    foreach ($this->landmarkService->getAllLandmarks() as $l) {
        if ($l->landmark_slug !== $slug) {
            $full = $this->landmarkService->getLandmarkById($l->landmark_id);
            if ($full) $otherLandmarks[] = $full;
        }
    }

    $this->view('History/HistoryDetail', [
        'title'          => $landmark->name . ' - Haarlem History',
        'landmark'       => $landmark,
        'introImage'     => $introImage,
        'historyImage'   => $historyImage,
        'whyVisitImage'  => $whyVisitImage,
        'otherLandmarks' => $otherLandmarks
    ]);
}


    #[RequireRole([UserRole::ADMIN])]
    public function editTourRoute($vars = []): void
    {
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
    }

    #[RequireRole([UserRole::ADMIN])]
    public function updateTourRoute($vars = []): void
    {
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
    }

}


