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
    private TicketRepository $ticketRepository;

    /**
     * Wires up the MediaRepository (for potential future media operations) and
     * TicketRepository (used by getSchedulesByRestaurant to attach ticket types per slot).
     */
    public function __construct()
    {
        $this->mediaRepository = new MediaRepository();
        $this->ticketRepository = new TicketRepository();
    }

    /**
     * Returns the shared SELECT … FROM SCHEDULE … LEFT JOIN … string used by most read methods.
     * Centralises the long multi-table JOIN so individual query methods only need to append
     * a WHERE clause, keeping the SQL consistent and reducing duplication.
     *
     * @return string  A complete SQL fragment from SELECT through to the last LEFT JOIN.
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
                v.venue_image_id as venue_image_id,

                -- Venue Media fields
                venue_media.media_id as venue_media_id,
                venue_media.file_path as venue_media_file_path,
                venue_media.alt_text as venue_media_alt_text,

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

                -- Artist Media fields
                artist_media.media_id as artist_media_id,
                artist_media.file_path as artist_media_file_path,
                artist_media.alt_text as artist_media_alt_text,

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

                -- Session field
                rs.session_number,

                -- Restaurant Media fields
                restaurant_media.media_id as restaurant_media_id,
                restaurant_media.file_path as restaurant_media_file_path,
                restaurant_media.alt_text as restaurant_media_alt_text,

                -- Restaurant Media fields
                restaurant_media.media_id as restaurant_media_id,
                restaurant_media.file_path as restaurant_media_file_path,
                restaurant_media.alt_text as restaurant_media_alt_text,


                -- Landmark fields (DB-compatible + keeps expected aliases)
                l.landmark_id,
                l.name as landmark_name,
                l.name as landmark_title,
                l.short_description as landmark_short_description,
                -- l.has_detail_page as landmark_has_detail_page, (column removed from DB)
                l.landmark_slug,
                l.main_image_id,

                -- Landmark Media fields
                landmark_media.media_id as landmark_media_id,
                landmark_media.file_path as landmark_media_file_path,
                landmark_media.alt_text as landmark_media_alt_text,

                -- Event Category fields
                ec.event_id as event_category_id,
                ec.type as event_category_type,
                ec.title as event_category_title,
                ec.category_description as event_category_description,
                ec.slug as event_category_slug

            FROM SCHEDULE s
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN MEDIA venue_media ON v.venue_image_id = venue_media.media_id
            LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
            LEFT JOIN MEDIA artist_media ON a.profile_image_id = artist_media.media_id
            LEFT JOIN RESTAURANT r ON s.restaurant_id = r.restaurant_id
            LEFT JOIN RESTAURANT_SESSION rs ON r.restaurant_id = rs.restaurant_id
            LEFT JOIN MEDIA restaurant_media ON r.main_image_id = restaurant_media.media_id
            LEFT JOIN LANDMARK l ON s.landmark_id = l.landmark_id
            LEFT JOIN MEDIA landmark_media ON l.main_image_id = landmark_media.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
        ";
    }

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

            return new Schedule()->hydrateSchedule($row);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching schedule by ID: " . $e->getMessage(), 0, $e);
        }
    }

    public function getAllSchedules(?string $eventType = null, ?string $date = null): array
    {
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery();
            $conditions = [];
            $params = [];

            if ($eventType !== null && $eventType !== '') {
                $conditions[] = "ec.type = :event_type";
                $params[':event_type'] = $eventType;
            }

            if ($date !== null && $date !== '') {
                $conditions[] = "s.date = :date";
                $params[':date'] = $date;
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY s.date ASC, s.start_time ASC";

            $stmt = $pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn($row) => new Schedule()->hydrateSchedule($row), $rows);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching all schedules: " . $e->getMessage(), 0, $e);
        }
    }

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

            return new Schedule()->hydrateSchedule($row);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching single schedule by event: " . $e->getMessage(), 0, $e);
        }
    }

    public function getSchedulesByEventId(int $eventId): array
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

            return array_map(fn($row) => new Schedule()->hydrateSchedule($row), $rows);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching schedule by event: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Returns all schedule slots for an event where the linked artist is flagged as a special event act.
     * Uses a targeted JOIN query (rather than the base query) to filter on ARTIST.special_event = 1,
     * and only selects the columns needed to keep the payload lean.
     * Used to populate the "back-to-back specials" section on Jazz event pages.
     *
     * @param int $eventId  The event to search within.
     *
     * @return Schedule[]  All special-event artist slots, ordered by date then start time.
     *
     * @throws PDOException  If the database query fails.
     */
    public function getBackToBackSpecialsByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT
                    s.*,
                    -- Artist Data
                    a.name AS artist_name, a.slug AS artist_slug, a.special_event AS artist_special_event,
                    -- Artist Media (Required by hydrateSchedule)
                    m.media_id AS artist_media_id,
                    m.file_path AS artist_media_file_path,
                    m.alt_text AS artist_media_alt_text,
                    -- Venue Data
                    v.name AS venue_name, v.street_address AS venue_address,
                    -- Event Category (Required by hydrateEventCategory)
                    ec.type AS event_category_type,
                    ec.title AS event_title
                FROM SCHEDULE s
                JOIN ARTIST a ON s.artist_id = a.artist_id
                JOIN VENUE v ON s.venue_id = v.venue_id
                JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                WHERE s.event_id = :event_id
                AND a.special_event = 1
                ORDER BY s.date ASC, s.start_time ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn($row) => new Schedule()->hydrateSchedule($row), $rows);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching back-to-back specials: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Returns all schedule slots linked to a specific restaurant, with ticket types attached to each slot.
     * Iterates the result set and calls TicketRepository per row, so the caller gets fully enriched
     * Schedule objects ready for display on a restaurant's booking page.
     *
     * @param int $restaurantId  The restaurant whose schedule slots you want.
     *
     * @return Schedule[]  All schedule slots for the restaurant, ordered by date then start time,
     *                     each with its ticketTypes array populated.
     */
    public function getSchedulesByRestaurant(int $restaurantId): array
    {
        $pdo = $this->connect();
        $sql = $this->getBaseQuery() . '
            Where s.restaurant_id = :restaurant_id
            ORDER BY s.date, s.start_time
        ';
        $getSchedule = $pdo->prepare($sql);
        $getSchedule->execute([
            'restaurant_id' => $restaurantId
        ]);
        $schedules = [];
        while ($row = $getSchedule->fetch(PDO::FETCH_ASSOC)) {
            $schedule = (new Schedule())->hydrateSchedule($row);
            $schedule->ticketTypes = $this->ticketRepository->getTicketTypesByScheduleId($schedule->schedule_id);
            $schedules[] = $schedule;
        }
        return $schedules;
    }

    public function getAvailableDates(): array
    {
        try {
            $pdo = $this->connect();

            $query = "SELECT DISTINCT date FROM SCHEDULE WHERE date >= CURDATE() ORDER BY date ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn($row) => $row['date'], $rows);
        } catch (PDOException $e) {
            throw new PDOException("Error fetching available dates: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Inserts a new schedule row and writes the generated schedule_id back onto the Schedule object.
     * Dates and times are formatted to database-compatible strings before binding.
     *
     * @param Schedule $schedule  The schedule to persist; schedule_id is set on success.
     *
     * @return bool  True if the INSERT succeeded.
     *
     * @throws PDOException  If the database query fails.
     */
    public function create(Schedule $schedule): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO SCHEDULE (event_id, venue_id, artist_id, restaurant_id, landmark_id,
                    date, start_time, end_time, total_capacity)
                VALUES (:event_id, :venue_id, :artist_id, :restaurant_id, :landmark_id,
                    :date, :start_time, :end_time, :total_capacity)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':event_id',       $schedule->event_id,       PDO::PARAM_INT);
            $stmt->bindValue(':venue_id',        $schedule->venue_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':artist_id',       $schedule->artist_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':restaurant_id',   $schedule->restaurant_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':landmark_id',     $schedule->landmark_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':date',            $schedule->date?->format('Y-m-d'));
            $stmt->bindValue(':start_time',      $schedule->start_time?->format('H:i:s'));
            $stmt->bindValue(':end_time',        $schedule->end_time?->format('H:i:s'));
            $stmt->bindValue(':total_capacity',  $schedule->total_capacity, PDO::PARAM_INT);

            $result = $stmt->execute();
            if ($result) {
                $schedule->schedule_id = (int)$pdo->lastInsertId();
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error creating schedule: " . $e->getMessage());
            throw new PDOException("Failed to create schedule: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Updates every editable field on an existing schedule row identified by schedule_id.
     * DateTime objects are formatted to strings before binding so PDO receives clean values.
     *
     * @param Schedule $schedule  The schedule with updated values and a valid schedule_id.
     *
     * @return bool  True if the UPDATE executed successfully.
     *
     * @throws PDOException  If the database query fails.
     */
    public function update(Schedule $schedule): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                UPDATE SCHEDULE SET
                    event_id       = :event_id,
                    venue_id       = :venue_id,
                    artist_id      = :artist_id,
                    restaurant_id  = :restaurant_id,
                    landmark_id    = :landmark_id,
                    date           = :date,
                    start_time     = :start_time,
                    end_time       = :end_time,
                    total_capacity = :total_capacity
                WHERE schedule_id = :schedule_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':schedule_id',     $schedule->schedule_id,    PDO::PARAM_INT);
            $stmt->bindValue(':event_id',        $schedule->event_id,       PDO::PARAM_INT);
            $stmt->bindValue(':venue_id',        $schedule->venue_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':artist_id',       $schedule->artist_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':restaurant_id',   $schedule->restaurant_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':landmark_id',     $schedule->landmark_id ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':date',            $schedule->date?->format('Y-m-d'));
            $stmt->bindValue(':start_time',      $schedule->start_time?->format('H:i:s'));
            $stmt->bindValue(':end_time',        $schedule->end_time?->format('H:i:s'));
            $stmt->bindValue(':total_capacity',  $schedule->total_capacity, PDO::PARAM_INT);


            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating schedule: " . $e->getMessage());
            throw new PDOException("Failed to update schedule: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Permanently deletes a schedule row by its primary key.
     * This is a hard delete — there is no soft-delete mechanism on the SCHEDULE table.
     *
     * @param int $scheduleId  The primary key of the schedule to remove.
     *
     * @return bool  True if the DELETE executed successfully.
     *
     * @throws PDOException  If the database query fails.
     */
    public function delete(int $scheduleId): bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare("DELETE FROM SCHEDULE WHERE schedule_id = :schedule_id");
            $stmt->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting schedule: " . $e->getMessage());
            throw new PDOException("Failed to delete schedule: " . $e->getMessage(), 0, $e);
        }
    }

    public function getAllEventCategories(): array
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->query("SELECT event_id, type, title FROM EVENT_CATEGORIES ORDER BY title ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event categories: " . $e->getMessage());
            throw new PDOException("Failed to fetch event categories: " . $e->getMessage(), 0, $e);
        }
    }
}
