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
        
          // Leer el JSON que envía nuestro JavaScript (AJAX) - (Lecture 6)
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        // Verificar que sí llegaron datos
        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No data received']);
            exit();
        }

        if($data['qtyNormal'] > 0)
        {
            $data['ticketSchemeEnum'] = TicketSchemeEnum::HISTORY_SINGLE_TICKET;
            $ticketNormalDTO = new TicketSelectionDTO($data);

            $ticketNormalType = $this->ticketService->getTicketTypeFromSelection($ticketNormalDTO);

            $orderItem = (new OrderItem())->createOrderItemFromTicketType($jsonData['quantity'], $ticketNormalType);
            $this->orderService->addOrderItemToSessionCart($orderItem);
        }
        elseif ($data['qtyFamily'] > 0)
        {
            $data['ticketSchemeEnum'] = TicketSchemeEnum::HISTORY_FAMILY_TICKET;
            $ticketFamilyDTO = new TicketSelectionDTO($data);

            $ticketFamilyType = $this->ticketService->getTicketTypeFromSelection($ticketFamilyDTO);

            $orderItem = (new OrderItem())->createOrderItemFromTicketType($jsonData['quantity'], $ticketFamilyType);
            $this->orderService->addOrderItemToSessionCart($orderItem);
        }

        // 2. Validamos usando los métodos del objeto// esto esta mal que este por alla abajo
        if (!$ticketNormalDTO->hasTickets() && !$ticketFamilyDTO->hasTickets()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid quantities']);
            exit();
        }

            $cart = $this->orderService->getSessionCart();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cart' => $cart
            ], JSON_PRETTY_PRINT);
        } 
    

    private function calculateRealTotal(int $qtyNormal, int $qtyFamily): float {
        
        // Aquí llamas a tu Servicio que se conecta a la Base de Datos
        
        $ticketOptions = $this->getTicketOptions();

        // Hacemos el cálculo
        $total = ($qtyNormal * $ticketOptions['normal']) + ($qtyFamily * $ticketOptions['family']);

        return $total;
    }


}


