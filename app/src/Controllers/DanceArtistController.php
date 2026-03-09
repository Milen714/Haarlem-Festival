<?php
namespace App\Controllers;

use App\Models\Schedule;
use App\Services\ArtistService;
use App\Repositories\ArtistRepository;
use App\Services\MediaService;
use App\Repositories\MediaRepository;
use App\Services\ScheduleService;
use App\Repositories\ScheduleRepository;
use App\Services\AlbumService;
use App\Repositories\AlbumRepository;
use App\Controllers\BaseController;
use App\Models\Gallery;

class DanceArtistController extends BaseController
{
    private const DANCE_EVENT_ID = 4;

    private ArtistService $artistService;
    private ScheduleService $scheduleService;
    private AlbumService $albumService;
    private MediaService $mediaService;

    public function __construct()
    {
        $this->mediaService = new MediaService(new MediaRepository());
        $this->artistService = new ArtistService(new ArtistRepository(), $this->mediaService);
        $this->scheduleService = new ScheduleService(new ScheduleRepository());
        $this->albumService = new AlbumService(new AlbumRepository());
    }

    public function artistDetail(array $vars = []): void
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
            $album = $this->albumService->getAlbumsByArtistId($artist->artist_id);
            $gallery = $artist->gallery;
            $scheduleByDate = $this->scheduleService->getSchedulesForArtistInEvent(
                (int) $artist->artist_id,
                self::DANCE_EVENT_ID
            );
            $vm = new \App\ViewModels\Dance\ArtistDetailViewModel($artist, $scheduleByDate, $album, $gallery);
            $this->view('Dance/artist-detail', [
                'vm' => $vm
            ]);
        } catch (\Exception $e) {
            error_log("Dance artist detail error: " . $e->getMessage());
            $this->notFound();
        }
    }
}