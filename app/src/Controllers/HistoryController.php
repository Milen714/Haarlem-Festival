<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;          
use App\Controllers\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Services\Interfaces\IHistoryService;
use App\Services\HistoryService;
use App\ViewModels\History\TicketHistoryViewModel;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private IHistoryService $historyService;

    const HISTORY_SLUG = 'events-history'; 

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->landmarkService = new LandmarkService();
        $this->historyService = new HistoryService();
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

        // Limpiar y validar los datos (Seguridad Básica - Lecture 2)
        $date = htmlspecialchars($data['date'] ?? '');
        $language = htmlspecialchars($data['language'] ?? '');
        $qtyNormal = filter_var($data['qtyNormal'] ?? 0, FILTER_VALIDATE_INT);
        $qtyFamily = filter_var($data['qtyFamily'] ?? 0, FILTER_VALIDATE_INT);

        if ($qtyNormal === 0 && $qtyFamily === 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid quantities.']);
            exit();
        }

        // 3. SEGURIDAD VITAL: Definir los precios reales en el Servidor
        // ¡Nunca confiamos en los precios que vienen de JavaScript porque pueden ser hackeados!
        // --- RESPUESTA A TU PREGUNTA 1: Calcular en un método aparte ---
        // Llamamos a nuestro nuevo método privado pasándole las cantidades
        $total = $this->calculateRealTotal($qtyNormal, $qtyFamily);

        // 4. Crear el "Carrito" en la sesión si aún no existe (Lecture Sessions)
        // --- RESPUESTA A TU PREGUNTA 2: Usar el modelo OrderItem ---
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }



        // Armar el "Item" o producto que acaban de elegir
        // Instanciamos tu modelo real (Asegúrate de haberle hecho 'use App\Models\OrderItem;' arriba)
        $cartItem = new OrderItem();
        
        // Rellenamos el modelo con los datos
        // (Ajusta los nombres de las propiedades según como estén en tu clase OrderItem)
        $cartItem->eventId = 'HistoryTour'; // o el ID numérico de la BD
        $cartItem->date = $date;
        $cartItem->language = $language;
        $cartItem->qtyNormal = $qtyNormal;
        $cartItem->qtyFamily = $qtyFamily;
        $cartItem->itemTotal = $total;

        // Guardamos EL OBJETO entero en la sesión del carrito
        $_SESSION['cart'][] = $cartItem;

        // Devolver una respuesta exitosa al JavaScript en formato JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    }

    private function calculateRealTotal(int $qtyNormal, int $qtyFamily): float {
        
        // Aquí llamas a tu Servicio que se conecta a la Base de Datos
        
        $ticketOptions = $this->getTicketOptions();

        // Hacemos el cálculo
        $total = ($qtyNormal * $ticketOptions['normal']) + ($qtyFamily * $ticketOptions['family']);

        return $total;
    }


}


