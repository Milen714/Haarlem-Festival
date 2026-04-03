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

    /** Empty constructor; all properties are populated via fromPDOData() and the hydrate*() methods. */
    public function __construct() {}

    /**
     * Hydrates the core Schedule fields from a raw database row.
     * Safely parses date and time strings into DateTime objects, accepting either
     * a pre-parsed DateTime or a plain string. Foreign-key IDs are also stored
     * here so they can be used to load nested objects separately if needed.
     *
     * @param array $data  A single SCHEDULE row from PDO::FETCH_ASSOC.
     *
     * @return void
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
     * Attaches a hydrated Venue object to this schedule if venue data is present in the row.
     * Skips silently when no venue_id is found, which is valid for non-venue schedules.
     *
     * @param array $data  The same database row passed to fromPDOData(), expected to contain venue columns.
     *
     * @return void
     */
    public function hydrateVenue(array $data): void
    {
        if (isset($data['venue_id']) && $data['venue_id'] !== null) {
            $this->venue = new Venue();
            $this->venue->fromPDOData($data);
        }
    }

    /**
     * Attaches a hydrated Artist object to this schedule if artist data is present in the row.
     * Skips silently when no artist_id is found, which is valid for non-music schedules.
     *
     * @param array $data  The same database row passed to fromPDOData(), expected to contain artist columns.
     *
     * @return void
     */
    public function hydrateArtist(array $data): void
    {
        if (isset($data['artist_id']) && $data['artist_id'] !== null) {
            $this->artist = new Artist();
            $this->artist->fromPDOData($data);
        }
    }

    /**
     * Attaches a hydrated Restaurant object to this schedule if restaurant data is present in the row.
     * Skips silently when no restaurant_id is found.
     *
     * @param array $data  The same database row passed to fromPDOData(), expected to contain restaurant columns.
     *
     * @return void
     */
    public function hydrateRestaurant(array $data): void
    {
        if (isset($data['restaurant_id']) && $data['restaurant_id'] !== null) {
            $this->restaurant = new Restaurant();
            $this->restaurant->fromPDOData($data);
        }
    }

    /**
     * Attaches a hydrated Landmark object to this schedule if landmark data is present in the row.
     * Skips silently when no landmark_id is found.
     *
     * @param array $data  The same database row passed to fromPDOData(), expected to contain landmark columns.
     *
     * @return void
     */
    public function hydrateLandmark(array $data): void
    {
        if (isset($data['landmark_id']) && $data['landmark_id'] !== null) {
            $this->landmark = new Landmark();
            $this->landmark->fromPDOData($data);
        }
    }

    /**
     * Attaches a hydrated EventCategory object if event category type data is present in the row.
     * The category tells you which festival strand (Jazz, Yummy, Dance, etc.) this slot belongs to.
     *
     * @param array $data  The same database row passed to fromPDOData(), expected to contain event_category_type.
     *
     * @return void
     */
    public function hydrateEventCategory(array $data): void
    {
        if (isset($data['event_category_type']) && $data['event_category_type'] !== null) {
            $this->event_category = new EventCategory();
            $this->event_category->fromPDOData($data);
        }
    }

    /**
     * Convenience method that runs all five hydrate*() calls in one shot.
     * Use this after fromPDOData() when the row comes from the base query that JOINs
     * all related tables (venue, artist, restaurant, landmark, event category).
     *
     * @param array $data  A fully joined schedule row from PDO::FETCH_ASSOC.
     *
     * @return void
     */
    public function hydrateAllRelations(array $data): void
    {
        $this->hydrateVenue($data);
        $this->hydrateArtist($data);
        $this->hydrateRestaurant($data);
        $this->hydrateLandmark($data);
        $this->hydrateEventCategory($data);
    }

    /**
     * Calculates the schedule slot's length in minutes based on start_time and end_time.
     * Returns null if either time is missing so callers can handle the "unknown duration" case.
     *
     * @return float|null  Duration in minutes (e.g. 90.0), or null if times are not set.
     */
    // tickets_sold and is_sold_out logic removed; now handled by TicketType
    public function getDurationInMinutes(): ?float
    {
        if ($this->start_time && $this->end_time) {
            $interval = $this->start_time->diff($this->end_time);
            return (float)(($interval->h * 60) + $interval->i);
        }
        return null;
    }

    /**
     * Returns the slot duration converted to hours, or null if either time is not set.
     * Delegates to getDurationInMinutes() so the two values are always consistent.
     *
     * @return float|null  Duration in hours (e.g. 1.5), or null if times are not set.
     */
    public function getDurationInHours(): ?float
    {
        $minutes = $this->getDurationInMinutes();
        return $minutes !== null ? $minutes / 60 : null;
    }

    /**
     * Factory that builds a fully hydrated Schedule from a single joined database row.
     * Calls fromPDOData() for core fields, then hydrateAllRelations() for nested objects,
     * and finally attaches Artist, Restaurant, and Landmark media directly from the
     * already-joined columns — avoiding extra queries per row.
     *
     * @param array $row  A fully joined row from the ScheduleRepository base query.
     *
     * @return Schedule  A new Schedule instance with all relations and media populated.
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
