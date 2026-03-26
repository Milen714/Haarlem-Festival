<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\MusicEvent\JazzArtistDetailViewModel;
use App\Services\Interfaces\JazzServiceInterface;
use App\Services\TicketService;

class JazzService implements JazzServiceInterface
{
    private const JAZZ_PAGE_SLUG = 'events-jazz';
    private const JAZZ_ARTIST_PAGE_SLUG = 'events-jazz-artist';

    private PageService $pageService;
    private ArtistService $artistService;
    private VenueService $venueService;
    private ScheduleService $scheduleService;
    private TicketService $ticketService;

    /**
     * Wires up all collaborating services needed to build Jazz page data:
     * PageService for CMS content, ArtistService and VenueService for lineup and locations,
     * ScheduleService for performance slots, and TicketService for day-pass ticket types.
     */
    public function __construct()
    {
        $this->pageService = new PageService();
        $this->artistService = new ArtistService();
        $this->venueService = new VenueService();
        $this->scheduleService = new ScheduleService();
        $this->ticketService = new TicketService();
    }

    /**
     * Venue loading uses a three-step fallback: event query → extract from schedule objects →
     * filter all venues by event category. This guards against DB linkage gaps where venues
     * exist in SCHEDULE rows but aren't linked via the event query's JOIN path.
     */
    public function loadJazzOverview(): array
    {
        $jazzPageData = $this->loadPageBySlugOrFail(self::JAZZ_PAGE_SLUG, 'Jazz page');
        $jazzEventId = $this->extractEventIdOrFail($jazzPageData, self::JAZZ_PAGE_SLUG);
        $allSchedules = $this->scheduleService->getSchedulesByEventId($jazzEventId);
        $performancesByDate = $this->groupSchedulesByDate($allSchedules);

        $venues = $this->venueService->getVenuesByEventId($jazzEventId);
        $venuesFromEventQueryCount = count($venues);
        if (empty($venues)) {
            $venueIds = [];
            $scheduleVenueMap = [];
            foreach ($allSchedules as $schedule) {
                $scheduleVenueId = (int) ($schedule->venue_id ?? 0);
                if ($scheduleVenueId <= 0 && isset($schedule->venue?->venue_id)) {
                    $scheduleVenueId = (int) $schedule->venue->venue_id;
                }

                if ($scheduleVenueId > 0) {
                    $venueIds[$scheduleVenueId] = true;
                }

                if (isset($schedule->venue) && !empty($schedule->venue->name)) {
                    $key = $scheduleVenueId > 0
                        ? (string) $scheduleVenueId
                        : 'name:' . strtolower(trim((string) $schedule->venue->name));
                    $scheduleVenueMap[$key] = $schedule->venue;
                }
            }

            foreach (array_keys($venueIds) as $venueId) {
                $venue = $this->venueService->getVenueById((int) $venueId);
                if ($venue !== null) {
                    $scheduleVenueMap[(string) $venueId] = $venue;
                }
            }

            $venues = array_values($scheduleVenueMap);
            $venuesFromSchedulesCount = count($venues);

            usort($venues, static function ($a, $b) {
                $aName = strtolower(trim((string) ($a->name ?? '')));
                $bName = strtolower(trim((string) ($b->name ?? '')));
                return $aName <=> $bName;
            });

            if (empty($venues)) {
                $allVenues = $this->venueService->getAllVenues();
                $venues = array_values(array_filter(
                    $allVenues,
                    static fn($v) => (int) ($v->event_category?->event_id ?? 0) === $jazzEventId
                ));

                if (empty($venues)) {
                    $venues = $allVenues;
                }
            }

            error_log(sprintf(
                'Jazz venues fallback debug: event_id=%d, schedules=%d, event_query=%d, from_schedules=%d, final=%d',
                $jazzEventId,
                count($allSchedules),
                $venuesFromEventQueryCount,
                $venuesFromSchedulesCount,
                count($venues)
            ));
        }

        $passTicketTypes = $this->ticketService->getTicketTypesBySchemeEnums(['JAZZ_DAY_PASS']);

        return [
            'title' => $jazzPageData->title ?? 'Jazz Event',
            'pageData' => $jazzPageData,
            'sections' => $jazzPageData->content_sections ?? [],
            'artists' => $this->artistService->getArtistsByEventId($jazzEventId),
            'venues' => $venues,
            'scheduleByDate' => $performancesByDate,
            'passTicketTypes' => $passTicketTypes,
        ];
    }

    public function loadJazzSchedule(): array
    {
        $jazzPageData = $this->loadPageBySlugOrFail(self::JAZZ_PAGE_SLUG, 'Jazz page');
        $jazzEventId = $this->extractEventIdOrFail($jazzPageData, self::JAZZ_PAGE_SLUG);
        $allSchedules = $this->scheduleService->getSchedulesByEventId($jazzEventId);

        return [
            'title' => 'Jazz Festival Schedule',
            'scheduleByDate' => $this->groupSchedulesByDate($allSchedules),
        ];
    }

    /**
     * Artist access is guarded by isArtistInEvent() — an artist who exists in the DB but
     * isn't booked for Jazz returns a 404, not their profile. This prevents cross-event leakage.
     */
    public function loadJazzArtistProfile(string $artistSlug): array
    {
        if ($artistSlug === '') {
            throw new ResourceNotFoundException('Artist not found.');
        }

        $jazzArtistPageData = $this->loadPageBySlugOrFail(self::JAZZ_ARTIST_PAGE_SLUG, 'Jazz artist page');
        $jazzEventId = $this->extractEventIdOrFail($jazzArtistPageData, self::JAZZ_ARTIST_PAGE_SLUG);

        $requestedArtist = $this->artistService->getArtistBySlug($artistSlug);

        if (!$requestedArtist) {
            throw new ResourceNotFoundException('Artist not found.');
        }

        if (!$this->artistService->isArtistInEvent((int) $requestedArtist->artist_id, $jazzEventId)) {
            throw new ResourceNotFoundException('Artist not found.');
        }

        $artistViewModel = new JazzArtistDetailViewModel(
            artist: $requestedArtist,
            scheduleByDate: $this->scheduleService->getSchedulesForArtistInEvent(
                (int) $requestedArtist->artist_id,
                $jazzEventId
            )
        );

        $passTicketTypes = $this->ticketService->getTicketTypesBySchemeEnums(['JAZZ_DAY_PASS']);

        return [
            'title'          => $artistViewModel->title,
            'vm'             => $artistViewModel,
            'pageData'       => $jazzArtistPageData,
            'sections'       => $jazzArtistPageData->content_sections ?? [],
            'passTicketTypes' => $passTicketTypes,
        ];
    }

    /**
     * Centralises the "page not found" guard so individual load methods stay readable.
     * Throws ResourceNotFoundException when the page is missing or has no page_id.
     */
    private function loadPageBySlugOrFail(string $pageSlug, string $pageName): object
    {
        $page = $this->pageService->getPageBySlug($pageSlug);

        if (empty($page->page_id)) {
            throw new ResourceNotFoundException($pageName . ' not found.');
        }

        return $page;
    }

    /**
     * Groups schedules by Y-m-d key and sorts chronologically so templates can iterate in order.
     */
    private function groupSchedulesByDate(array $schedules): array
    {
        $grouped = [];
        foreach ($schedules as $schedule) {
            $dateKey = $schedule->date ? $schedule->date->format('Y-m-d') : 'unknown';
            $grouped[$dateKey][] = $schedule;
        }
        ksort($grouped);
        return $grouped;
    }

    /**
     * Every Jazz page must have an event category set in the CMS; without it we cannot
     * know which artists, venues, and schedules to load — throws ApplicationException.
     */
    private function extractEventIdOrFail(object $pageData, string $pageSlug): int
    {
        $eventId = $pageData->event_category->event_id ?? null;

        if ($eventId === null) {
            throw new ApplicationException("Jazz page '{$pageSlug}' is missing an event category.");
        }

        return (int) $eventId;
    }
}
