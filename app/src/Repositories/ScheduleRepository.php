<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Schedule;
use App\Repositories\Interfaces\IScheduleRepository;
use PDO;
use PDOException;

class ScheduleRepository extends Repository implements IScheduleRepository
{
    private MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository();
    }

    /**
     * Base query with all JOINs for nested objects (no media joins)
     */
    private function getBaseQuery(): string
    {
        return "
            SELECT 
                s.schedule_id,
                s.event_id,
                s.date,
                s.start_time,
                s.end_time,
                s.total_capacity,
                s.tickets_sold,
                s.is_sold_out,
                s.artist_id,
                s.restaurant_id,
                s.landmark_id,
                
                -- Venue fields
                v.venue_id,
                v.name as venue_name,
                v.street_address as venue_address,
                v.city as venue_city,
                v.postal_code as venue_postal_code,
                v.country as venue_country,
                v.description_html as venue_description_html,
                v.capacity as venue_capacity,
                v.phone as venue_phone,
                v.email as venue_email,
                
                -- Artist fields
                a.artist_id,
                a.name as artist_name,
                a.slug as artist_slug,
                a.bio as artist_bio,
                a.gallery_id as artist_gallery_id,
                a.website as artist_website,
                a.spotify_url as artist_spotify_url,
                a.youtube_url as artist_youtube_url,
                a.soundcloud_url as artist_soundcloud_url,
                a.featured_quote as artist_featured_quote,
                a.press_quote as artist_press_quote,
                a.profile_image_id as artist_profile_image_id,
                a.collaborations as artist_collaborations,
                a.deleted_at as artist_deleted_at,
                
                -- Restaurant fields
                r.restaurant_id,
                r.name as restaurant_name,
                r.short_description as restaurant_short_description,
                r.welcome_text as restaurant_welcome_text,
                r.price_category as restaurant_price_category,
                r.stars as restaurant_stars,
                r.review_count as restaurant_review_count,
                r.website_url as restaurant_website_url,
                r.main_image_id as restaurant_main_image_id,
                
                -- Landmark fields
                l.landmark_id,
                l.name as landmark_name,
                l.landmark_title,
                l.short_description as landmark_short_description,
                l.has_detail_page as landmark_has_detail_page,
                l.landmark_slug,
                l.landmark_image_id,
                
                -- Event Category fields
                ec.event_id as event_category_id,
                ec.type as event_category_type,
                ec.title as event_category_title,
                ec.category_description as event_category_description,
                ec.slug as event_category_slug
                
            FROM SCHEDULE s
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
            LEFT JOIN RESTAURANT r ON s.restaurant_id = r.restaurant_id
            LEFT JOIN LANDMARK l ON s.landmark_id = l.landmark_id
            LEFT JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
        ";
    }

    /**
     * Gets a single schedule by ID
     * @return Schedule|null
     */
    public function getScheduleById(int $scheduleId): ?Schedule
    {
        try {
            $pdo = $this->connect();
            
            $query = $this->getBaseQuery() . " WHERE s.schedule_id = :schedule_id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':schedule_id', $scheduleId, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$row) {
                return null;
            }
            
            return $this->hydrateSchedule($row);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching schedule by ID: " . $e->getMessage());
        }
    }

    /**
     * Gets all schedules
     * @return Schedule[]
     */
    public function getAllSchedules(): array
    {
        try {
            $pdo = $this->connect();
            
            $query = $this->getBaseQuery() . " ORDER BY s.date ASC, s.start_time ASC";

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map(fn($row) => $this->hydrateSchedule($row), $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching all schedules: " . $e->getMessage());
        }
    }

    /**
     * Gets a single schedule for an event (first upcoming one)
     * @return Schedule|null
     */
    public function getOneScheduleByEventId(int $eventId): ?Schedule
    {
        try {
            $pdo = $this->connect();
            
            $query = $this->getBaseQuery() . "
                WHERE s.event_id = :event_id
                ORDER BY s.date ASC, s.start_time ASC
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$row) {
                return null;
            }
            
            return $this->hydrateSchedule($row);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching single schedule by event: " . $e->getMessage());
        }
    }

    /**
     * Gets all the schedules for a specific event
     * @return Schedule[]
     */
    public function getScheduleByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();
            
            $query = $this->getBaseQuery() . "
                WHERE s.event_id = :event_id
                ORDER BY s.date ASC, s.start_time ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map(fn($row) => $this->hydrateSchedule($row), $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching schedule by event: " . $e->getMessage());
        }
    }

    /**
     * Hydrate a Schedule object from a database row
     * All nested objects (venue, artist, restaurant, landmark) will be hydrated if data exists
     * Media objects are fetched separately using MediaRepository
     */
    private function hydrateSchedule(array $row): Schedule
    {
        $schedule = new Schedule();
        $schedule->fromPDOData($row);
        $schedule->hydrateAllRelations($row);
        
        // Hydrate media for Artist
        if ($schedule->artist !== null && isset($row['artist_profile_image_id']) && $row['artist_profile_image_id'] !== null) {
            $schedule->artist->profile_image = $this->mediaRepository->getMediaById((int)$row['artist_profile_image_id']);
        }
        
        // Hydrate media for Restaurant
        if ($schedule->restaurant !== null && isset($row['restaurant_main_image_id']) && $row['restaurant_main_image_id'] !== null) {
            $schedule->restaurant->main_image = $this->mediaRepository->getMediaById((int)$row['restaurant_main_image_id']);
        }
        
        // Hydrate media for Landmark
        if ($schedule->landmark !== null && isset($row['landmark_image_id']) && $row['landmark_image_id'] !== null) {
            $schedule->landmark->landmark_image = $this->mediaRepository->getMediaById((int)$row['landmark_image_id']);
        }
        
        return $schedule;
    }
}