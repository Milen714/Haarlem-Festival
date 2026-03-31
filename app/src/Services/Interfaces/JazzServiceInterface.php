<?php

namespace App\Services\Interfaces;

interface JazzServiceInterface
{
    /**
     * Builds all data needed to render the main Jazz overview page.
     * Returns page content, artist lineup, venues, schedule grouped by date,
     * and day-pass ticket types for the purchase overlay.
     *
     * @return array{title: string, pageData: object, sections: array, artists: array, venues: array, scheduleByDate: array, passTicketTypes: array}
     */
    public function loadJazzOverview(): array;

    /**
     * Builds the data array for the Jazz schedule page.
     * Returns only the title and all performances grouped by date — no artist bios or venue detail.
     *
     * @return array{title: string, scheduleByDate: array}
     */
    public function loadJazzSchedule(): array;

    /**
     * Builds all data needed to render a single Jazz artist's detail page.
     * Validates the slug, checks the artist belongs to the Jazz event, then assembles
     * a view model with schedule slots and day-pass ticket types.
     *
     * @param string $artistSlug  The URL slug of the artist to display, e.g. 'miles-davis'.
     *
     * @return array{title: string, vm: \App\Models\MusicEvent\JazzArtistDetailViewModel, pageData: object, sections: array, passTicketTypes: array}
     */
    public function loadJazzArtistProfile(string $artistSlug): array;
}
