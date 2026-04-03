<?php

namespace App\Models\MusicEvent;

use App\Models\Gallery;

class JazzArtistDetailViewModel
{
    public string $title;
    public Artist $artist;
    public array $scheduleByDate;
    public ?Gallery $gallery = null;

    /**
     * Assembles all data needed to render the Jazz artist detail page.
     * Derives the page title from the artist's name and promotes the gallery
     * reference to a top-level property so templates don't have to drill into
     * the artist object just to check for photos.
     *
     * @param Artist $artist         The fully hydrated artist, including albums and profile image.
     * @param array  $scheduleByDate Performance slots for this artist, grouped by date string (Y-m-d)
     *                               as returned by ScheduleService::getSchedulesForArtistInEvent().
     */
    public function __construct(
        Artist $artist,
        array  $scheduleByDate = []
    ) {
        $this->artist         = $artist;
        $this->scheduleByDate = $scheduleByDate;
        $this->title          = $artist->name ?? 'Jazz Artist';
        $this->gallery        = $artist->gallery;
    }

    /**
     * Returns true if the artist has a gallery with at least one media item attached.
     * Use this in templates to decide whether to render the photo gallery section.
     *
     * @return bool  True if there is at least one image in the gallery, false otherwise.
     */
    public function hasGallery(): bool
    {
        return $this->gallery !== null
            && !empty($this->gallery->media_items);
    }
}
