<?php
namespace App\Controllers;

use App\Services\Interfaces\IPageService;
use App\Services\PageService;
use App\Framework\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;

class HistoryController extends BaseController
{
    private IPageService $pageService;
    private ILandmarkService $landmarkService;
    private ILogService $logService;

    const HISTORY_SLUG = 'events-history';

    public function __construct()
    {
        $this->pageService = new PageService();
        $this->landmarkService = new LandmarkService();
        $this->logService = new LogService();
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
            $this->logService->exception('History', $e);
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
            $hero = null;
            $tourInfo = null;
            $bookTour = null;
            $tickets = null;
            $tourFeatures = [];     
            $goodToKnow = null; 

            foreach ($sections as $s) {
                $type = $s->section_type->value;
                
                if ($type === 'tour_info') { 
                    $tourInfo = $s;
                } elseif ($type === 'tour_tickets') { 
                    $tickets = $s;
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
                'tickets'         => $tickets,
                'tourFeatures'    => $tourFeatures,    
                'goodToKnow'      => $goodToKnow  
            ]);

        } catch (\Exception $e) {
            $this->logService->exception('History', $e);
            $this->internalServerError();
        }
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

}


