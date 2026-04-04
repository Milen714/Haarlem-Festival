<?php
namespace App\ViewModels\Dance;

use App\Models\Gallery;
use App\ViewModels\Dance\BaseViewModel;

class ArtistDetailViewModel extends BaseViewModel
{
    public $artist;
    public array $upcomingEvents;
    public array $albums;
    public ?Gallery $gallery;

    /**
     * Assembles all data needed to render the Dance artist detail page.
     * Calls the parent constructor to initialise the base breadcrumb trail with 'Home',
     * then appends Dance-specific breadcrumbs: a link to the Dance homepage and a
     * non-linked label for the artist's own name.
     *
     * @param mixed    $artist         The fully hydrated Artist model, including profile image.
     * @param array    $scheduleByDate The artist's performance slots grouped by date (from ScheduleService).
     * @param array    $album          The artist's discography as an array of Album objects.
     * @param Gallery|null $gallery    The artist's photo gallery, or null if they have none.
     */
    public function __construct($artist, $scheduleByDate, $album, $gallery)
    {
        // Initialize Base (adds 'Home' breadcrumb)
        parent::__construct();

        $this->artist = $artist;
        $this->upcomingEvents = $scheduleByDate;
        $this->albums = $album;
        $this->gallery = $gallery;

        // Build the navigation trail
        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($artist->name);
    }
}
