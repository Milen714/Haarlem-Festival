<?php
namespace App\Controllers;

use App\Models\Schedule;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Services\ScheduleService;
use App\Services\AlbumService;
use App\Services\TicketService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IAlbumService;
use App\Services\Interfaces\ITicketService;
use App\Framework\BaseController;
use App\Models\Gallery;

class DanceArtistController extends BaseController
{
    private const DANCE_EVENT_ID = 4;

    private IArtistService $artistService;
    private IScheduleService $scheduleService;
    private IAlbumService $albumService;
    private IMediaService $mediaService;
    private ITicketService $ticketService;

    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->artistService = new ArtistService();
        $this->scheduleService = new ScheduleService();
        $this->albumService = new AlbumService();
        $this->ticketService = new TicketService();
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
            $ticketLookup = $this->getTicketLookupForSchedules($scheduleByDate);
            $vm = new \App\ViewModels\Dance\ArtistDetailViewModel($artist, $scheduleByDate, $album, $gallery);
            $this->view('Dance/artist-detail', [
                'vm' => $vm,
                'ticketLookup' => $ticketLookup
            ]);
        } catch (\Exception $e) {
            error_log("Dance artist detail error: " . $e->getMessage());
            $this->notFound();
        }
    }
    private function getTicketLookupForSchedules(array $groupedSchedules): array
    {
        $lookup = [];
        
        foreach ($groupedSchedules as $date => $slots) {
            
            foreach ($slots as $session) {
                
                $scheduleId = $session['schedule_id'] ?? null;
                
                if ($scheduleId) {
                    $tickets = $this->ticketService->getTicketTypesByScheduleId((int)$scheduleId);
                    $ticket = $tickets[0] ?? null;

                    if ($ticket) {
                        $lookup[$scheduleId] = [
                            'id' => $ticket->ticket_type_id,
                            'price' => $ticket->ticket_scheme->price ?? 0.0,
                            'available' => ($ticket->capacity - $ticket->tickets_sold)
                        ];
                    }
                }
            }
        }
        return $lookup;
    }
}