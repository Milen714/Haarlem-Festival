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
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
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
    private ILogService $logService;

    /**
     * Wires up all services needed to build a Dance artist detail page.
     * ArtistService fetches the artist and gallery, ScheduleService provides per-event slots,
     * AlbumService loads discography, MediaService handles any media operations,
     * and TicketService is used to build the per-schedule ticket availability lookup.
     */
    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->artistService = new ArtistService();
        $this->scheduleService = new ScheduleService();
        $this->albumService = new AlbumService();
        $this->ticketService = new TicketService();
        $this->logService = new LogService();
    }

    /**
     * Renders the Dance artist detail page for the given URL slug.
     * Fetches the artist, their albums, gallery, and schedule slots for the Dance event,
     * then builds a ticket availability lookup so the view can show pricing and sold-out state.
     * Responds with a 404 if the slug is missing, the artist does not exist, or any error occurs.
     *
     * @param array $vars  Route variables — expects a 'slug' key with the artist's URL slug.
     *
     * @return void
     */
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
            $this->logService->exception('Dance', $e);
            $this->notFound();
        }
    }

    /**
     * Builds a flat schedule_id → ticket info lookup array for all slots across all dates.
     * For each slot, fetches the first available ticket type and extracts the price and remaining
     * availability. The view uses this to render a "Buy tickets" button with accurate data.
     *
     * @param array $groupedSchedules  Slots grouped by date, as returned by ScheduleService::getSchedulesForArtistInEvent().
     *
     * @return array<int, array{id: int, price: float, available: int}>
     *               Keyed by schedule_id; each value has the ticket_type_id, price, and seats left.
     */
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
