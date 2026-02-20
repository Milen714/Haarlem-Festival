<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\ArtistService;
use App\Repositories\PageRepository;
use App\Repositories\ArtistRepository;

class DanceController extends BaseController
{
    private PageService $pageService;
    private ArtistService $artistService;
    private PageRepository $pageRepository;
    
    // Dance event ID constant
    private const DANCE_EVENT_ID = 4;

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
        $pageData = $this->pageRepository->getPageBySlug('events-dance');

        $artists = $this->artistService->getArtistsByEventId(self::DANCE_EVENT_ID);

        $venues = [];
        $sections = $pageData->content_sections ?? [];
        
        $organizedSections = $this->organizeSections($sections);
        
        $this->view('Dance/index', [
            'title' => $pageData->title ?? 'Dance Event',
            'pageData' => $pageData,
            'sections' => $sections,
            'heroSection' => $organizedSections['heroSection'],
            'artistSection' => $organizedSections['artistSection'],
            'specialSection' => $organizedSections['specialSection'],
            'venueSection' => $organizedSections['venueSection'],
            'ticketSection' => $organizedSections['ticketSection'],
            'gallerySection' => $organizedSections['gallerySection'],
            'artists' => $artists,
            'venues' => $venues
        ]);
    }
    private function organizeSections(array $sections): array
    {
        $organized = [
            'heroSection' => null,
            'artistSection' => null,
            'specialSection' => null,
            'venueSection' => null,
            'ticketSection' => null,
            'gallerySection' => null,
        ];
        
        foreach ($sections as $section) {
            switch ($section->section_type->value) {
                case 'hero_picture':
                case 'hero_gallery':
                    $organized['heroSection'] = $section;
                    break;
                case 'text':
                    if (stripos($section->title, 'Artist') !== false) {
                        $organized['artistSection'] = $section;
                    } elseif (stripos($section->title, 'Special') !== false) {
                        $organized['specialSection'] = $section;
                    } elseif (stripos($section->title, 'Venue') !== false) {
                        $organized['venueSection'] = $section;
                    } elseif (stripos($section->title, 'Ticket') !== false) {
                        $organized['ticketSection'] = $section;
                    }  elseif (stripos($section->title, 'Gallery') !== false) {
                        $organized['gallerySection'] = $section;
                    } 
                    break;
            }
        }
        
        return $organized;
    }

}