<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Repositories\PageRepository;
use App\Repositories\ArtistRepository;
use App\CmsModels\Enums\PageType;

class JazzController extends BaseController
{
    private PageService $pageService;
    private ArtistService $artistService;
    private PageRepository $pageRepository;
    
    // Jazz event ID constant
    private const JAZZ_EVENT_ID = 3;

    public function __construct()
    {
        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);
        
        // Initialize ArtistService
        $artistRepository = new ArtistRepository();
        $this->artistService = new ArtistService($artistRepository);
    }

    public function index($vars = [])
    {
        // Get the Jazz landing page data from database
        $pageData = $this->pageService->getPageData(PageType::event_landing);
        
        // Get artists for Jazz event through service
        $artists = $this->artistService->getArtistsByEventId(self::JAZZ_EVENT_ID);
        
        // For now using empty arrays for other dynamic data
        $scheduleByDate = [];
        $venues = [];

        // Extract sections for easier access in view
        $sections = $pageData->content_sections ?? [];
        
        // Organize sections by type for cleaner view access
        $organizedSections = $this->organizeSections($sections);
        
        // Pass data to view
        $this->view('Jazz/index', [
            'title' => $pageData->title ?? 'Jazz Event',
            'pageData' => $pageData,
            'sections' => $sections,
            'heroSection' => $organizedSections['heroSection'],
            'aboutSection' => $organizedSections['aboutSection'],
            'artistSection' => $organizedSections['artistSection'],
            'scheduleSection' => $organizedSections['scheduleSection'],
            'venueSection' => $organizedSections['venueSection'],
            'ticketSection' => $organizedSections['ticketSection'],
            'artists' => $artists,
            'scheduleByDate' => $scheduleByDate,
            'venues' => $venues
        ]);
    }
    
    /**
     * Organize page sections by type for easier access in views
     * @param array $sections
     * @return array
     */
    private function organizeSections(array $sections): array
    {
        $organized = [
            'heroSection' => null,
            'aboutSection' => null,
            'artistSection' => null,
            'scheduleSection' => null,
            'venueSection' => null,
            'ticketSection' => null,
            'gallerySection' => null,
        ];
        
        foreach ($sections as $section) {
            switch ($section->section_type) {
                case 'hero_picture':
                case 'hero_gallery':
                    $organized['heroSection'] = $section;
                    break;
                case 'text':
                    if (stripos($section->title, 'About') !== false) {
                        $organized['aboutSection'] = $section;
                    } elseif (stripos($section->title, 'Artist') !== false) {
                        $organized['artistSection'] = $section;
                    } elseif (stripos($section->title, 'Glance') !== false || stripos($section->title, 'Schedule') !== false) {
                        $organized['scheduleSection'] = $section;
                    } elseif (stripos($section->title, 'Venue') !== false) {
                        $organized['venueSection'] = $section;
                    } elseif (stripos($section->title, 'Ticket') !== false) {
                        $organized['ticketSection'] = $section;
                    }
                    break;
            }
        }
        
        return $organized;
    }
}