<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;          
use App\Controllers\BaseController;
use App\Models\Enums\TicketSchemeEnum;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Services\Interfaces\IHistoryService;
use App\Services\HistoryService;
use App\ViewModels\History\TicketHistoryViewModel;
use App\Models\History\TicketSelectionDTO;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Models\Payment\OrderItem;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private IHistoryService $historyService;
    private ITicketService $ticketService;
    private IOrderService $orderService;

    const HISTORY_SLUG = 'events-history'; 

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->landmarkService = new LandmarkService();
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
            $landmarks = [];

            foreach ($sections as $s) {
                $type = $s->section_type->value;
                if ($type === 'welcome') {
                    $welcome = $s;
                } elseif ($type === 'landmark') {
                    $landmarks[] = $s;
                } elseif ($type === 'book_tour') {
                    $bookTour = $s;
                }
                elseif ($type === 'hero_picture') { 
                    $hero = $s;
                }
            }

            
            $this->view('History/HistoryHomepage', [
                'pageData'  => $pageData,
                'hero'      => $hero,
                'welcome'   => $welcome,
                'landmarks' => $landmarks,
                'bookTour'  => $bookTour
            ]);

        } catch (\Exception $e) {
            error_log("History error: " . $e->getMessage());
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
                }
            }

            $this->view('History/HistoryTour', [
                'pageData'        => $pageData,
                'hero'            => $hero,
                'tourInfo'        => $tourInfo,
                'bookTour'        => $bookTour,
                'ticketOptions'   => $ticketOptions,
                'tourFeatures'    => $tourFeatures,    
                'goodToKnow'      => $goodToKnow  
            ]);

        } catch (\Exception $e) {
            error_log("History Tour error: " . $e->getMessage());
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

        //if gallery exists and has the media items, the media items are taken out and assigned to variables for the view
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

        //pass the images to the view as well
        $this->view('History/HistoryDetail', [
            'title' => $landmark->name . ' - Haarlem History',
            'landmark' => $landmark,
            'introImage' => $introImage,
            'historyImage' => $historyImage,
            'whyVisitImage' => $whyVisitImage
        ]);
    
    }

    public function addHistoryToCart() {
        
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No data received']);
            exit();
        }

        $addedAny = false;

        // 1. Procesar Ticket Normal
        if (!empty($data['qtyNormal']) && $data['qtyNormal'] > 0 && !empty($data['normalTicketId'])) {
            // Usamos tu método exacto del servicio
            $ticketNormalType = $this->ticketService->getTicketTypeById($data['normalTicketId']);
            
            // Accedemos a la propiedad pública is_sold_out directamente
            if ($ticketNormalType && !$ticketNormalType->is_sold_out) {
                $orderItem = (new OrderItem())->createOrderItemFromTicketType($data['qtyNormal'], $ticketNormalType);
                $this->orderService->addOrderItemToSessionCart($orderItem);
                $addedAny = true;
            } else {
                echo json_encode(['success' => false, 'message' => 'Normal ticket is sold out.']);
                exit();
            }
        }

        // 2. Procesar Ticket Familiar
        if (!empty($data['qtyFamily']) && $data['qtyFamily'] > 0 && !empty($data['familyTicketId'])) {
            // Usamos tu método exacto del servicio
            $ticketFamilyType = $this->ticketService->getTicketTypeById($data['familyTicketId']);
            
            // Accedemos a la propiedad pública is_sold_out directamente
            if ($ticketFamilyType && !$ticketFamilyType->is_sold_out) {
                $orderItem = (new OrderItem())->createOrderItemFromTicketType($data['qtyFamily'], $ticketFamilyType);
                $this->orderService->addOrderItemToSessionCart($orderItem);
                $addedAny = true;
            } else {
                echo json_encode(['success' => false, 'message' => 'Family ticket is sold out.']);
                exit();
            }
        }

        // Si no se añadió nada, las cantidades eran inválidas
        if (!$addedAny) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid quantities']);
            exit();
        }

        // Retornar éxito
        $cart = $this->orderService->getSessionCart();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart' => $cart
        ], JSON_PRETTY_PRINT);
    }

}


