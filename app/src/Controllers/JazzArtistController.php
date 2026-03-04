<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MusicEvent\JazzArtistDetailViewModel;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Repositories\ScheduleRepository;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Services\ScheduleService;

class JazzArtistController extends BaseController
{
    private const JAZZ_EVENT_ID = 3;

    private ArtistService $artistService;
    private ScheduleService $scheduleService;

    public function __construct()
    {
        $mediaService = new MediaService(new MediaRepository());

        $this->artistService   = new ArtistService(new ArtistRepository(), $mediaService);
        $this->scheduleService = new ScheduleService(new ScheduleRepository());
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

            $this->view('Jazz/artist-detail', [
                'title' => $vm->title,
                'vm'    => $vm,
            ]);

        } catch (\Throwable $e) {
            error_log('Jazz artist detail error: ' . $e->getMessage());
            error_log($e->getTraceAsString());
            $this->internalServerError();
        }
    }
}