<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MusicEvent\JazzArtistDetailViewModel;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Repositories\PageRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Services\ArtistService;
use App\Services\LandmarkService;
use App\Services\MediaService;
use App\Services\PageService;
use App\Services\RestaurantService;
use App\Services\ScheduleService;
use App\Services\VenueService;

class JazzArtistController extends BaseController
{
    private const JAZZ_EVENT_ID = 3;
    private const ARTIST_PAGE_SLUG = 'events-jazz-artist';

    private ArtistService $artistService;
    private ScheduleService $scheduleService;
    private PageService $pageService;

    public function __construct()
    {
        $mediaService = new MediaService(new MediaRepository());

        $this->artistService = new ArtistService(new ArtistRepository(), $mediaService);

        $this->scheduleService = new ScheduleService(
            new ScheduleRepository(),
            new VenueService(new VenueRepository(), $mediaService),
            $this->artistService,
            new RestaurantService(new RestaurantRepository(), $mediaService),
            new LandmarkService()
        );

        $this->pageService = new PageService(new PageRepository());
    }

    public function detail(array $vars = []): void
    {
        $slug = $vars['slug'] ?? null;

        if (!$slug) {
            $this->notFound();
            return;
        }

        try {
            $artist = $this->artistService->getArtistBySlug($slug);

            if (!$artist) {
                $this->notFound();
                return;
            }

            if (!$this->artistService->isArtistInEvent((int) $artist->artist_id, self::JAZZ_EVENT_ID)) {
                $this->notFound();
                return;
            }

            $scheduleByDate = $this->scheduleService->getSchedulesForArtistInEvent(
                (int) $artist->artist_id,
                self::JAZZ_EVENT_ID
            );

            $vm = new JazzArtistDetailViewModel(
                artist: $artist,
                scheduleByDate: $scheduleByDate
            );

            // Fetch CMS page sections for the artist detail template
            $pageData = null;
            $sections = [];
            try {
                $pageData = $this->pageService->getPageBySlug(self::ARTIST_PAGE_SLUG);
                $sections = $pageData->content_sections ?? [];
            } catch (\Throwable $e) {
                error_log('Jazz artist page sections not found: ' . $e->getMessage());
            }

            $this->view('Jazz/artist-detail', [
                'title'    => $vm->title,
                'vm'       => $vm,
                'pageData' => $pageData,
                'sections' => $sections,
            ]);

        } catch (\Throwable $e) {
            error_log('Jazz artist detail error: ' . $e->getMessage());
            error_log($e->getTraceAsString());
            $this->internalServerError();
        }
    }

}