<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\MusicEvent\JazzArtistDetailViewModel;
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
        $this->pageService = new PageService();
        $this->artistService = new ArtistService();
        $this->venueService = new VenueService();
        $this->scheduleService = new ScheduleService();
    }

    /**
     * Load the jazz festival overview page with featured artists and venues
     */
    public function loadJazzOverview(): array
    {
        $jazzPageData = $this->loadPageBySlugOrFail(self::JAZZ_PAGE_SLUG, 'Jazz page');
        $jazzEventId = $this->extractEventIdOrFail($jazzPageData, self::JAZZ_PAGE_SLUG);

        return [
            'title' => $jazzPageData->title ?? 'Jazz Event',
            'pageData' => $jazzPageData,
            'sections' => $jazzPageData->content_sections ?? [],
            'artists' => $this->artistService->getArtistsByEventId($jazzEventId),
            'venues' => $this->venueService->getVenuesByEventId($jazzEventId),
            'scheduleByDate' => [],
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
