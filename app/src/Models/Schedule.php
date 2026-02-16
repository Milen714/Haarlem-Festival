<?php
namespace App\Models;

use App\Models\Enums\EventType;
use App\Models\EventCategory;
use App\Models\Venue;
use App\Models\Restaurant;
use App\Models\MusicEvent\Artist;
use App\Models\History\Landmark;
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
    public ?int $tickets_sold = null;
    public ?bool $is_sold_out = null;
    
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
        $this->tickets_sold = isset($data['tickets_sold']) ? (int)$data['tickets_sold'] : null;
        $this->is_sold_out = isset($data['is_sold_out']) ? (bool)$data['is_sold_out'] : false;
    }

    /**
     * Hydrate the Venue object if venue data is available
     */
    public function hydrateVenue(array $data): void 
    {
        if (isset($data['venue_id']) && $data['venue_id'] !== null) {
            $this->venue = new Venue();
            $this->venue->fromPDOData([
                'venue_id' => $data['venue_id'],
                'name' => $data['venue_name'] ?? '',
                'street_address' => $data['venue_address'] ?? '',
                'city' => $data['venue_city'] ?? '',
                'postal_code' => $data['venue_postal_code'] ?? null,
                'country' => $data['venue_country'] ?? null,
                'description_html' => $data['venue_description_html'] ?? null,
                'capacity' => $data['venue_capacity'] ?? null,
                'phone' => $data['venue_phone'] ?? null,
                'email' => $data['venue_email'] ?? null,
            ]);
        }
    }

    /**
     * Hydrate the Artist object if artist data is available
     */
    public function hydrateArtist(array $data): void 
    {
        if (isset($data['artist_id']) && $data['artist_id'] !== null) {
            $this->artist = new Artist();
            $this->artist->fromPDOData([
                'artist_id' => $data['artist_id'],
                'name' => $data['artist_name'] ?? '',
                'slug' => $data['artist_slug'] ?? '',
                'bio' => $data['artist_bio'] ?? null,
                'website' => $data['artist_website'] ?? null,
                'spotify_url' => $data['artist_spotify_url'] ?? null,
                'youtube_url' => $data['artist_youtube_url'] ?? null,
                'soundcloud_url' => $data['artist_soundcloud_url'] ?? null,
                'featured_quote' => $data['artist_featured_quote'] ?? null,
                'press_quote' => $data['artist_press_quote'] ?? null,
                'profile_image_id' => $data['artist_profile_image_id'] ?? null,
                'profile_image_path' => $data['artist_profile_image_path'] ?? null,
                'profile_image_alt_text' => $data['artist_profile_image_alt'] ?? null,
            ]);
        }
    }

    /**
     * Hydrate the Restaurant object if restaurant data is available
     */
    public function hydrateRestaurant(array $data): void 
    {
        if (isset($data['restaurant_id']) && $data['restaurant_id'] !== null) {
            $this->restaurant = new Restaurant();
            $this->restaurant->fromPDOData([
                'restaurant_id' => $data['restaurant_id'],
                'name' => $data['restaurant_name'] ?? '',
                'short_description' => $data['restaurant_short_description'] ?? null,
                'welcome_text' => $data['restaurant_welcome_text'] ?? null,
                'price_category' => $data['restaurant_price_category'] ?? null,
                'stars' => $data['restaurant_stars'] ?? null,
                'review_count' => $data['restaurant_review_count'] ?? null,
                'website_url' => $data['restaurant_website_url'] ?? null,
                'main_image_id' => $data['restaurant_main_image_id'] ?? null,
                'restaurant_image_path' => $data['restaurant_image_path'] ?? null,
                'restaurant_image_alt' => $data['restaurant_image_alt'] ?? null,
            ]);
        }
    }

    /**
     * Hydrate the Landmark object if landmark data is available
     */
    public function hydrateLandmark(array $data): void 
    {
        if (isset($data['landmark_id']) && $data['landmark_id'] !== null) {
            $this->landmark = new Landmark();
            $this->landmark->fromPDOData([
                'landmark_id' => $data['landmark_id'],
                'name' => $data['landmark_name'] ?? '',
                'landmark_title' => $data['landmark_title'] ?? null,
                'short_description' => $data['landmark_short_description'] ?? null,
                'has_detail_page' => $data['landmark_has_detail_page'] ?? false,
                'landmark_slug' => $data['landmark_slug'] ?? null,
                'landmark_image_id' => $data['landmark_image_id'] ?? null,
                'landmark_image_path' => $data['landmark_image_path'] ?? null,
                'landmark_image_alt' => $data['landmark_image_alt'] ?? null,
            ]);
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
    }

    /**
     * Get the available capacity
     */
    public function getAvailableCapacity(): int 
    {
        return ($this->total_capacity ?? 0) - ($this->tickets_sold ?? 0);
    }

    /**
     * Check if tickets are available
     */
    public function hasAvailableTickets(): bool 
    {
        return !$this->is_sold_out && $this->getAvailableCapacity() > 0;
    }
}