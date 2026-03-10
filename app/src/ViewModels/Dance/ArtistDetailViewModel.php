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

    public function __construct($artist, $scheduleByDate, $album, $gallery)
    {
        // 1. Initialize Base (adds 'Home' breadcrumb)
        parent::__construct();
        
        $this->artist = $artist;
        $this->upcomingEvents = $scheduleByDate;
        $this->albums = $album;
        $this->gallery = $gallery;

        // 2. Build the navigation trail
        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($artist->name);
    }
}