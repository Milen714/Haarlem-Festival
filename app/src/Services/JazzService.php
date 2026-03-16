<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\MusicEvent\JazzArtistDetailViewModel;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Repositories\PageRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Services\Interfaces\JazzServiceInterface;

class JazzService implements JazzServiceInterface
{
    private const JAZZ_PAGE_SLUG = 'events-jazz';
    private const JAZZ_ARTIST_PAGE_SLUG = 'events-jazz-artist';

    private PageService $pageService;
    private ArtistService $artistService;
    private VenueService $venueService;
    private ScheduleService $scheduleService;

    public function __construct()
    {
        $mediaService = new MediaService(new MediaRepository());

        $this->pageService = new PageService(new PageRepository());
        $this->artistService = new ArtistService(new ArtistRepository(), $mediaService);
        $this->venueService = new VenueService(new VenueRepository(), $mediaService);
        $this->scheduleService = new ScheduleService(
            new ScheduleRepository(),
            $this->venueService,
            $this->artistService,
            new RestaurantService(new RestaurantRepository(), $mediaService),
            new LandmarkService()
        );
    }

    /**
     * Load the jazz festival overview page with featured artists and venues
     */
    public function loadJazzOverview(): array
    {
        $jazzPageData = $this->loadPageBySlugOrFail(self::JAZZ_PAGE_SLUG, 'Jazz page');
        $jazzEventId = $this->extractEventIdOrFail($jazzPageData, self::JAZZ_PAGE_SLUG);
        $allSchedules = $this->scheduleService->getSchedulesByEventId($jazzEventId);

        $performancesByDate = [];
        foreach ($allSchedules as $schedule) {
            $dateKey = $schedule->date ? $schedule->date->format('Y-m-d') : 'unknown';
            $performancesByDate[$dateKey][] = $schedule;
        }

        ksort($performancesByDate);

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

                // Last-resort fallback to keep homepage populated while mappings are repaired.
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

        return [
            'title' => $jazzPageData->title ?? 'Jazz Event',
            'pageData' => $jazzPageData,
            'sections' => $jazzPageData->content_sections ?? [],
            'artists' => $this->artistService->getArtistsByEventId($jazzEventId),
            'venues' => $venues,
            'scheduleByDate' => $performancesByDate,
        ];
    }

    /**
     * Load the jazz festival schedule organized by performance date
     */
    public function loadJazzSchedule(): array
    {
        $jazzPageData = $this->loadPageBySlugOrFail(self::JAZZ_PAGE_SLUG, 'Jazz page');
        $jazzEventId = $this->extractEventIdOrFail($jazzPageData, self::JAZZ_PAGE_SLUG);
        $allSchedules = $this->scheduleService->getSchedulesByEventId($jazzEventId);

        // Group performances by date for easier display
        $performancesByDate = [];
        foreach ($allSchedules as $schedule) {
            $dateKey = $schedule->date ? $schedule->date->format('Y-m-d') : 'unknown';
            $performancesByDate[$dateKey][] = $schedule;
        }

        ksort($performancesByDate);

        return [
            'title' => 'Jazz Festival Schedule',
            'scheduleByDate' => $performancesByDate,
        ];
    }

    /**
     * Load a specific jazz artist's profile with their performance schedule
     *
     * @throws ResourceNotFoundException if artist not found or doesn't perform at jazz event
     * @throws ApplicationException if jazz event data is misconfigured
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

        // Verify this artist performs at the jazz event
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

        return [
            'title' => $artistViewModel->title,
            'vm' => $artistViewModel,
            'pageData' => $jazzArtistPageData,
            'sections' => $jazzArtistPageData->content_sections ?? [],
        ];
    }

    /**
     * Load a page by its slug, ensure it exists, or throw exception
     *
     * @throws ResourceNotFoundException if page not found
     */
    private function loadPageBySlugOrFail(string $pageSlug, string $pageName): object
    {
        $page = $this->pageService->getPageBySlug($pageSlug);

        if (empty($page->page_id)) {
            throw new ResourceNotFoundException($pageName . ' not found.');
        }

        return $page;
    }

    private function extractEventIdOrFail(object $pageData, string $pageSlug): int
    {
        $eventId = $pageData->event_category->event_id ?? null;

        if ($eventId === null) {
            throw new ApplicationException("Jazz page '{$pageSlug}' is missing an event category.");
        }

        return (int) $eventId;
    }
}
