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

    /**
     * Fetches all available tour options from the database and builds a
     * TicketHistoryViewModel grouped by language → date → time, with
     * separate IDs for normal and family ticket types.
     *
     * @return TicketHistoryViewModel
     */
    public function getAvailableTourOptions(): TicketHistoryViewModel
    {
        $rawOptions = $this->historyRepository->getAvailableTourOptions();
        $ticketPrices = $this->historyRepository->getTourTicketPrices();
        
        $viewModel = new TicketHistoryViewModel();

        $ticketOptions = [];

        foreach ($rawOptions as $row) {
            $language   = $row['language'];
            $date       = $row['date'];
            $time       = $row['time'];
            $id         = (int)$row['ticket_type_id'];
            $schemeType = $row['scheme_enum'];

            $ticketOptions[$language] ??= [];
            $ticketOptions[$language][$date] ??= [];
            $ticketOptions[$language][$date][$time] ??= ['normalId' => null, 'familyId' => null];

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

    /**
     * Returns all data needed to render the History homepage:
     * page content sections (hero, welcome, book_tour) and the
     * featured landmarks with resolved image paths.
     *
     * @return array<string, mixed>
     */
    public function getHomepageData(): array
    {
        $pageData  = $this->pageService->getPageBySlug('events-history');
        $landmarks = $this->landmarkService->getFeaturedLandmarks();

        $s        = $this->extractSections($pageData->content_sections ?? [], ['hero_picture', 'welcome', 'book_tour']);
        $hero     = $s['hero_picture'];
        $welcome  = $s['welcome'];
        $bookTour = $s['book_tour'];

        foreach ($landmarks as $lm) {
            $lm->imagePath = $this->resolveImagePath($lm->main_image_id?->file_path);
        }

        return compact('pageData', 'landmarks', 'hero', 'welcome', 'bookTour');
    }

    /**
     * Returns all data needed to render the History Tour page:
     * page sections, ticket options, decoded route stops, and tour features.
     *
     * @return array<string, mixed>
     */
    public function getTourData(): array
    {
        $pageData      = $this->pageService->getPageBySlug('history-tour');
        $ticketOptions = $this->getAvailableTourOptions();
        $sections      = $pageData->content_sections ?? [];

        $s            = $this->extractSections($sections, ['hero_picture', 'tour_info', 'book_tour', 'good_to_know', 'tour_route', 'text', 'tour_tickets']);
        $hero         = $s['hero_picture'];
        $tourInfo     = $s['tour_info'];
        $bookTour     = $s['book_tour'];
        $goodToKnow   = $s['good_to_know'];
        $tourRoute    = $s['tour_route'];
        $text         = $s['text'];
        $tourTickets  = $s['tour_tickets'];
        $tourFeatures = array_values(array_filter($sections, fn($sec) => $sec->section_type->value === 'tour_features'));

        $stopsData  = ($tourRoute && $tourRoute->content_html) ? json_decode($tourRoute->content_html, true) : [];
        $routeStops = array_column($stopsData, 'name');
        $totalStops = count($routeStops);

        return compact('pageData', 'ticketOptions', 'hero', 'tourInfo', 'bookTour', 'goodToKnow', 'tourRoute', 'text', 'tourTickets', 'tourFeatures', 'routeStops', 'totalStops');
    }

    /**
     * Returns all data needed to render a landmark detail page.
     * Throws LandmarkNotFoundException if the slug does not match any landmark.
     *
     * @param  string $slug  URL slug of the landmark
     * @return array<string, mixed>
     * @throws LandmarkNotFoundException
     */
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
        $imageKeys   = ['introImage', 'historyImage', 'whyVisitImage'];
        $images      = array_fill_keys($imageKeys, $placeholder);
        if (!empty($landmark->gallery->media_items)) {
            $items = array_values($landmark->gallery->media_items);
            foreach ($imageKeys as $i => $key) {
                if (isset($items[$i]->media)) {
                    $images[$key] = '/' . ltrim($items[$i]->media->file_path, '/');
                }
            }
        }
        [$introImage, $historyImage, $whyVisitImage] = array_values($images);

        $otherLandmarks = array_values(array_filter(
            $this->landmarkService->getAllLandmarksWithDetails(),
            fn($l) => $l->landmark_slug !== $slug
        ));

        foreach ($otherLandmarks as $other) {
            $items = !empty($other->gallery->media_items) ? array_values($other->gallery->media_items) : [];
            $other->imagePath = $this->resolveImagePath($items[0]->media->file_path ?? null);
        }

        return compact('landmark', 'introImage', 'historyImage', 'whyVisitImage', 'otherLandmarks', 'bookTour', 'welcome');
    }

    public function getTourRouteSection(): ?PageSection
    {
        $pageData = $this->pageService->getPageBySlug('history-tour');
        $s = $this->extractSections($pageData->content_sections ?? [], ['tour_route']);
        return $s['tour_route'];
    }

    /**
     * Returns a web-safe image path. Falls back to the placeholder if
     * $filePath is null or empty.
     *
     * @param  string|null $filePath     Raw file path from the database
     * @param  string      $placeholder  Fallback image URL
     * @return string
     */
    private function resolveImagePath(?string $filePath, string $placeholder = '/Assets/Home/ImagePlaceholder.png'): string
    {
        return $filePath ? '/' . ltrim($filePath, '/') : $placeholder;
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