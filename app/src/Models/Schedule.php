<?php
namespace App\Models;

use App\Models\Enums\EventType;
use App\Models\EventCategory;
use App\Models\Venue;
use App\Models\Restaurant;
use App\Models\MusicEvent\Artist;
use App\Models\Landmark;
use DateTime;

class Schedule
{
    public ?int $schedule_id = null;
    public ?int $event_id = null;
    public ?EventCategory $event_category = null;
    public ?DateTime $date = null;
    public ?DateTime $start_time = null;
    public ?DateTime $end_time = null;
    public ?int $total_capacity = null;
    
    // Foreign key IDs
    public ?int $venue_id = null;
    public ?int $artist_id = null;
    public ?int $restaurant_id = null;
    public ?int $landmark_id = null;
    
    // Nested objects (null if not set in database)
    public ?Venue $venue = null;
    public ?Artist $artist = null;
    public ?Restaurant $restaurant = null;
    public ?Landmark $landmark = null;
    public array $ticketTypes = [];

    public function __construct() {}

    /**
     * Hydrate the Schedule object from PDO data
     */
    public function fromPDOData(array $data): void 
    {
        $this->schedule_id = isset($data['schedule_id']) ? (int)$data['schedule_id'] : null;
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        
        // Handle date - could be string or DateTime
        if (isset($data['date'])) {
            $this->date = $data['date'] instanceof DateTime 
                ? $data['date'] 
                : new DateTime($data['date']);
        }
        
        // Handle start_time - could be string or DateTime
        if (isset($data['start_time'])) {
            $this->start_time = $data['start_time'] instanceof DateTime 
                ? $data['start_time'] 
                : new DateTime($data['start_time']);
        }
        
        // Handle end_time - could be string or DateTime  
        if (isset($data['end_time'])) {
            $this->end_time = $data['end_time'] instanceof DateTime 
                ? $data['end_time'] 
                : new DateTime($data['end_time']);
        }
        
        // Foreign key IDs
        $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
        $this->artist_id = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
        $this->restaurant_id = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $this->landmark_id = isset($data['landmark_id']) ? (int)$data['landmark_id'] : null;
        
        // Schedule specific fields
        $this->total_capacity = isset($data['total_capacity']) ? (int)$data['total_capacity'] : null;
    }

    /**
     * Hydrate the Venue object if venue data is available
     */
    public function hydrateVenue(array $data): void 
    {
        if (isset($data['venue_id']) && $data['venue_id'] !== null) {
            $this->venue = new Venue();
            $this->venue->fromPDOData($data);
        }
    }

    /**
     * Hydrate the Artist object if artist data is available
     */
    public function hydrateArtist(array $data): void 
    {
        if (isset($data['artist_id']) && $data['artist_id'] !== null) {
            $this->artist = new Artist();
            $this->artist->fromPDOData($data);
        }
    }

    /**
     * Hydrate the Restaurant object if restaurant data is available
     */
    public function hydrateRestaurant(array $data): void 
    {
        if (isset($data['restaurant_id']) && $data['restaurant_id'] !== null) {
            $this->restaurant = new Restaurant();
            $this->restaurant->fromPDOData($data);
        }
    }

    /**
     * Hydrate the Landmark object if landmark data is available
     */
    public function hydrateLandmark(array $data): void 
    {
        if (isset($data['landmark_id']) && $data['landmark_id'] !== null) {
            $this->landmark = new Landmark();
            $this->landmark->fromPDOData($data);
        }
    }

    /**
     * Hydrate the EventCategory object if event category data is available
     */
    public function hydrateEventCategory(array $data): void 
    {
        if (isset($data['event_category_type']) && $data['event_category_type'] !== null) {
            $this->event_category = new EventCategory();
            $this->event_category->fromPDOData($data);
        }
    }

    /**
     * Hydrate all nested objects at once
     */
    public function hydrateAllRelations(array $data): void 
    {
        $this->hydrateVenue($data);
        $this->hydrateArtist($data);
        $this->hydrateRestaurant($data);
        $this->hydrateLandmark($data);
        $this->hydrateEventCategory($data);
    }

    // tickets_sold and is_sold_out logic removed; now handled by TicketType
    public function getDurationInMinutes(): ?float
    {
        if ($this->start_time && $this->end_time) {
            $interval = $this->start_time->diff($this->end_time);
            return (float)(($interval->h * 60) + $interval->i);
        }
        return null;
    }

    public function getDurationInHours(): ?float
    {
        $minutes = $this->getDurationInMinutes();
        return $minutes !== null ? $minutes / 60 : null;
    }
    /**
     * Hydrate a Schedule object from a database row
     * All nested objects (venue, artist, restaurant, landmark) will be hydrated if data exists
     * Media objects are hydrated directly from the joined query results
     */
    public function hydrateSchedule(array $row): Schedule
    {
        $schedule = new Schedule();
        $schedule->fromPDOData($row);
        $schedule->hydrateAllRelations($row);
        
        // Hydrate media for Artist from joined data (no extra query needed)
        if ($schedule->artist !== null && isset($row['artist_media_id']) && $row['artist_media_id'] !== null) {
            $media = new \App\Models\Media();
            $media->media_id = (int)$row['artist_media_id'];
            $media->file_path = $row['artist_media_file_path'];
            $media->alt_text = $row['artist_media_alt_text'];
            $schedule->artist->profile_image = $media;
        }
        
        // Hydrate media for Restaurant from joined data (no extra query needed)
        if ($schedule->restaurant !== null && isset($row['restaurant_media_id']) && $row['restaurant_media_id'] !== null) {
            $media = new \App\Models\Media();
            $media->media_id = (int)$row['restaurant_media_id'];
            $media->file_path = $row['restaurant_media_file_path'];
            $media->alt_text = $row['restaurant_media_alt_text'];
            $schedule->restaurant->main_image = $media;
        }
        
        // Hydrate media for Landmark from joined data (no extra query needed)
        if ($schedule->landmark !== null && isset($row['landmark_media_id']) && $row['landmark_media_id'] !== null) {
            $media = new \App\Models\Media();
            $media->media_id = (int)$row['landmark_media_id'];
            $media->file_path = $row['landmark_media_file_path'];
            $media->alt_text = $row['landmark_media_alt_text'];
            $schedule->landmark->main_image_id = $media;
        }
        
        return $schedule;
    }
}