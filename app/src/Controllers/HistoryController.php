<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    const HISTORY_SLUG = 'events-history'; 

    public function __construct(IPageService $pageService)
    {
        $this->pageService = $pageService;
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
            $welcome = null;
            $cta = null;
            $landmarks = [];

            foreach ($sections as $s) {
                $type = $s->section_type->value;
                if ($type === 'text') {
                    $welcome = $s;
                } elseif ($type === 'landmark') {
                    $landmarks[] = $s;
                } elseif ($type === 'cta_block') {
                    $cta = $s;
                }
            }

            // Pasamos los datos empaquetados a la vista
            $this->view('History/HistoryHomepage', [
                'pageData'  => $pageData,
                'title'     => $title,
                'welcome'   => $welcome,
                'landmarks' => $landmarks,
                'cta'       => $cta
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
            $goodToKnowItems = [];  // <-- NUEVO: Para la lista

            foreach ($sections as $s) {
                $type = $s->section_type->value;
                if ($type === 'text') {
                    $tourInfo = $s;
                } elseif ($type === 'cta_block') {
                    $cta = $s;
                } elseif ($type === 'article') {
                    $tickets = $s;
                } elseif ($type === 'tour_feature') {
                    $tourFeatures[] = $s; // <-- Atrapamos las tarjetas
                } elseif ($type === 'tour_rule') {
                    $goodToKnowItems[] = $s; // <-- Atrapamos las reglas
                }
            }

            $this->view('History/HistoryTour', [
                'pageData'        => $pageData,
                'title'           => $pageData->title,
                'tourInfo'        => $tourInfo,
                'cta'             => $cta,
                'tickets'         => $tickets,
                'tourFeatures'    => $tourFeatures,    // Pasamos a la vista
                'goodToKnowItems' => $goodToKnowItems  // Pasamos a la vista
            ]);

        } catch (\Exception $e) {
            error_log("History Tour error: " . $e->getMessage());
            $this->internalServerError();
        }
    }
    }
}