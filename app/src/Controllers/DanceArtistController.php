<?php
namespace App\Controllers;

use App\Models\Schedule;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Services\ScheduleService;
use App\Services\AlbumService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IAlbumService;
use App\Controllers\BaseController;
use App\Models\Gallery;

class DanceArtistController extends BaseController
{
    private const DANCE_EVENT_ID = 4;

    private IArtistService $artistService;
    private IScheduleService $scheduleService;
    private IAlbumService $albumService;
    private IMediaService $mediaService;

    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->artistService = new ArtistService();
        $this->scheduleService = new ScheduleService();
        $this->albumService = new AlbumService();
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