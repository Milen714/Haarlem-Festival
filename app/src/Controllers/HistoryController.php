<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;          
use App\Repositories\PageRepository;

class HistoryController extends BaseController
{
    private PageService $pageService;
    const HISTORY_SLUG = 'events-history'; 

    public function __construct()
    {
        $this->pageService = new PageService(new PageRepository());
    }

    public function index($vars = [])
    {
        try {
            $pageData = $this->pageService->getPageBySlug(self::HISTORY_SLUG);
            
            if (!$pageData) {
                $this->notFound();
                return;
            }

            // Título de la página
            $title = $pageData->title; 

            // Extraemos y clasificamos las secciones
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
                } elseif ($type === 'bookTour') {
                    $bookTour = $s;
                }
                elseif ($type === 'hero_picture') { // <--- 2. Atrapamos el Hero
                    $hero = $s;
                }
            }

            // Pasamos los datos empaquetados a la vista
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

            $sections = $pageData->content_sections ?? [];
            $tourInfo = null;
            $cta = null;
            $tickets = null;
            $tourFeatures = [];     // <-- NUEVO: Para las 4 tarjetas
            $goodToKnow= null;  // <-- NUEVO: Para la lista

            
            foreach ($sections as $s) {
                $type = $s->section_type->value;
                if ($type === 'text') {
                    $tourInfo = $s;
                } elseif ($type === 'cta_block') {
                    $cta = $s;
                } elseif ($type === 'article') {
                    $tickets = $s;
                } elseif ($type === 'tour_features') {
                    $tourFeatures[] = $s; // <-- Atrapamos las tarjetas
                } elseif ($type === 'good_to_know') {
                    $goodToKnow = $s; // <-- Atrapamos las reglas
                }
                
            }

            $this->view('History/HistoryTour', [
                'pageData'        => $pageData,
                'title'           => $pageData->title,
                'tourInfo'        => $tourInfo,
                'cta'             => $cta,
                'tickets'         => $tickets,
                'tourFeatures'    => $tourFeatures,    // Pasamos a la vista
                'goodToKnow' => $goodToKnow  // Pasamos a la vista
            ]);

        } catch (\Exception $e) {
            error_log("History Tour error: " . $e->getMessage());
            $this->internalServerError();
        }
    }
}

