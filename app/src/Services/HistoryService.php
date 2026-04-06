<?php

namespace App\Services;

use App\Repositories\HistoryRepository;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\ILandmarkService;
use App\Services\PageService;
use App\Services\LandmarkService;
use App\ViewModels\History\TicketHistoryViewModel;
use App\Models\Enums\TicketSchemeEnum;
use App\Exceptions\LandmarkNotFoundException;

class HistoryService implements IHistoryService
{
    private HistoryRepository $historyRepository;
    private IPageService $pageService;
    private ILandmarkService $landmarkService;

    public function __construct()
    {
        $this->historyRepository = new HistoryRepository();
        $this->pageService       = new PageService();
        $this->landmarkService   = new LandmarkService();
    }

    public function getAvailableTourOptions(): TicketHistoryViewModel
    {
        $rawOptions = $this->historyRepository->getAvailableTourOptions();
        $ticketPrices = $this->historyRepository->getTourTicketPrices();
        
        $viewModel = new TicketHistoryViewModel();

        // Creamos el Árbol de Decisiones
        $ticketOptions = [];

        foreach ($rawOptions as $row) {
            $language = $row['language'];
            $date = $row['date'];
            $time = $row['time'];
            $id   = (int)$row['ticket_type_id']; 
            $schemeType = $row['scheme_enum']; 

            // Si el idioma no existe en el árbol, lo creamos
            if (!isset($ticketOptions[$language])) {
                $ticketOptions[$language] = [];
            }
            // Si la fecha no existe dentro de ese idioma, la creamos
            if (!isset($ticketOptions[$language][$date])) {
                $ticketOptions[$language][$date] = [];
            }
            
            // ¡EL CAMBIO IMPORTANTE ESTÁ AQUÍ!
            // Inicializamos la hora usando su llave explícita [$time]
            if (!isset($ticketOptions[$language][$date][$time])) {
                $ticketOptions[$language][$date][$time] = ['normalId' => null, 'familyId' => null];
            }

            // Asignamos el ID correcto usando validación estricta con tu Enum
            if ($schemeType === TicketSchemeEnum::HISTORY_SINGLE_TICKET->value) {
                $ticketOptions[$language][$date][$time]['normalId'] = $id;
            } elseif ($schemeType === TicketSchemeEnum::HISTORY_FAMILY_TICKET->value) {
                $ticketOptions[$language][$date][$time]['familyId'] = $id;
            }
        }

        $viewModel->options = $ticketOptions;
        $viewModel->normalPrice = $ticketPrices['normal'] ?? 0.00;
        $viewModel->familyPrice = $ticketPrices['family'] ?? 0.00;

        return $viewModel;
    }

    public function getHomepageData(): array
    {
        $pageData  = $this->pageService->getPageBySlug('events-history');
        $landmarks = $this->landmarkService->getFeaturedLandmarks();
        $hero = $welcome = $bookTour = null;

        foreach ($pageData->content_sections ?? [] as $s) {
            match($s->section_type->value) {
                'hero_picture' => $hero     = $s,
                'welcome'      => $welcome  = $s,
                'book_tour'    => $bookTour = $s,
                default        => null
            };
        }

        return compact('pageData', 'landmarks', 'hero', 'welcome', 'bookTour');
    }

    public function getTourData(): array
    {
        $pageData      = $this->pageService->getPageBySlug('history-tour');
        $ticketOptions = $this->getAvailableTourOptions();
        $hero = $tourInfo = $bookTour = $goodToKnow = $tourRoute = $text = null;
        $tourFeatures = [];

        foreach ($pageData->content_sections ?? [] as $s) {
            match($s->section_type->value) {
                'hero_picture'  => $hero          = $s,
                'tour_info'     => $tourInfo      = $s,
                'book_tour'     => $bookTour      = $s,
                'good_to_know'  => $goodToKnow    = $s,
                'tour_route'    => $tourRoute     = $s,
                'text'          => $text          = $s,
                'tour_features' => $tourFeatures[] = $s,
                default         => null
            };
        }

        return compact('pageData', 'ticketOptions', 'hero', 'tourInfo', 'bookTour', 'goodToKnow', 'tourRoute', 'text', 'tourFeatures');
    }

    public function getDetailData(string $slug): array
    {
        $landmark = $this->landmarkService->getLandmarkBySlug($slug);
        if (!$landmark) {
            throw new LandmarkNotFoundException("Landmark '{$slug}' not found.");
        }

        $pageData = $this->pageService->getPageBySlug('detail');
        $bookTour = $welcome = null;
        foreach ($pageData->content_sections ?? [] as $s) {
            match($s->section_type->value) {
                'book_tour' => $bookTour = $s,
                'welcome'   => $welcome  = $s,
                default     => null
            };
        }

        $placeholder = '/Assets/Home/ImagePlaceholder.png';
        $introImage = $historyImage = $whyVisitImage = $placeholder;
        if (!empty($landmark->gallery->media_items)) {
            $items = array_values($landmark->gallery->media_items);
            if (isset($items[0]->media)) $introImage    = '/' . ltrim($items[0]->media->file_path, '/');
            if (isset($items[1]->media)) $historyImage  = '/' . ltrim($items[1]->media->file_path, '/');
            if (isset($items[2]->media)) $whyVisitImage = '/' . ltrim($items[2]->media->file_path, '/');
        }

        $otherLandmarks = [];
        foreach ($this->landmarkService->getAllLandmarks() as $l) {
            if ($l->landmark_slug !== $slug) {
                $full = $this->landmarkService->getLandmarkById($l->landmark_id);
                if ($full) $otherLandmarks[] = $full;
            }
        }

        return compact('landmark', 'introImage', 'historyImage', 'whyVisitImage', 'otherLandmarks', 'bookTour', 'welcome');
    }

}