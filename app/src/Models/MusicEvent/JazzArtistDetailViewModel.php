<?php

namespace App\Models\MusicEvent;

use App\Models\Gallery;

class JazzArtistDetailViewModel
{
    public string $title;
    public Artist $artist;
    public array $scheduleByDate;
    public ?Gallery $gallery = null;

    public function __construct(
        Artist $artist,
        array  $scheduleByDate = []
    ) {
        $this->artist         = $artist;
        $this->scheduleByDate = $scheduleByDate;
        $this->title          = $artist->name ?? 'Jazz Artist';
        $this->gallery        = $artist->gallery;
    }

    public function hasGallery(): bool
    {
        return $this->gallery !== null
            && !empty($this->gallery->media_items);
    }
}