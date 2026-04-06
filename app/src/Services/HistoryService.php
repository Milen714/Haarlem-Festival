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
use App\CmsModels\PageSection;

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

        $s        = $this->extractSections($pageData->content_sections ?? [], ['hero_picture', 'welcome', 'book_tour']);
        $hero     = $s['hero_picture'];
        $welcome  = $s['welcome'];
        $bookTour = $s['book_tour'];

        return compact('pageData', 'landmarks', 'hero', 'welcome', 'bookTour');
    }

    public function getTourData(): array
    {
        $pageData      = $this->pageService->getPageBySlug('history-tour');
        $ticketOptions = $this->getAvailableTourOptions();
        $sections      = $pageData->content_sections ?? [];

        $s            = $this->extractSections($sections, ['hero_picture', 'tour_info', 'book_tour', 'good_to_know', 'tour_route', 'text']);
        $hero         = $s['hero_picture'];
        $tourInfo     = $s['tour_info'];
        $bookTour     = $s['book_tour'];
        $goodToKnow   = $s['good_to_know'];
        $tourRoute    = $s['tour_route'];
        $text         = $s['text'];
        $tourFeatures = array_values(array_filter($sections, fn($sec) => $sec->section_type->value === 'tour_features'));

        return compact('pageData', 'ticketOptions', 'hero', 'tourInfo', 'bookTour', 'goodToKnow', 'tourRoute', 'text', 'tourFeatures');
    }

    public function getDetailData(string $slug): array
    {
        $landmark = $this->landmarkService->getLandmarkBySlug($slug);
        if (!$landmark) {
            throw new LandmarkNotFoundException("Landmark '{$slug}' not found.");
        }

        $pageData = $this->pageService->getPageBySlug('detail');
        $s        = $this->extractSections($pageData->content_sections ?? [], ['book_tour', 'welcome']);
        $bookTour = $s['book_tour'];
        $welcome  = $s['welcome'];

        $placeholder = '/Assets/Home/ImagePlaceholder.png';
        $introImage = $historyImage = $whyVisitImage = $placeholder;
        if (!empty($landmark->gallery->media_items)) {
            $items = array_values($landmark->gallery->media_items);
            if (isset($items[0]->media)) $introImage    = '/' . ltrim($items[0]->media->file_path, '/');
            if (isset($items[1]->media)) $historyImage  = '/' . ltrim($items[1]->media->file_path, '/');
            if (isset($items[2]->media)) $whyVisitImage = '/' . ltrim($items[2]->media->file_path, '/');
        }

        $otherLandmarks = array_values(array_filter(
            $this->landmarkService->getAllLandmarksWithDetails(),
            fn($l) => $l->landmark_slug !== $slug
        ));

        return compact('landmark', 'introImage', 'historyImage', 'whyVisitImage', 'otherLandmarks', 'bookTour', 'welcome');
    }

    public function getTourRouteSection(): ?PageSection
    {
        $pageData = $this->pageService->getPageBySlug('history-tour');
        $s = $this->extractSections($pageData->content_sections ?? [], ['tour_route']);
        return $s['tour_route'];
    }

    private function extractSections(array $sections, array $keys): array
    {
        $result = array_fill_keys($keys, null);
        foreach ($sections as $s) {
            $type = $s->section_type->value;
            if (array_key_exists($type, $result)) {
                $result[$type] = $s;
            }
        }
        return $result;
    }
}