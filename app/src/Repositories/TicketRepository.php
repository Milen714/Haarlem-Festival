<?php
namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TicketType;
use App\Repositories\Interfaces\ITicketRepository;
use PDO;
use PDOException;


class TicketRepository extends Repository implements ITicketRepository
{
    /**
     * Base query with ticket type fields and full schedule hydration aliases.
     */
    private function getBaseQuery(): string
    {
        return "
            SELECT
                tt.ticket_type_id,
                s.schedule_id,
                tt.name as ticket_name,
                tt.price,
                tt.description,
                tt.reservation_fee,
                tt.min_age,
                tt.max_age,
                tt.min_quantity,
                tt.max_quantity,
                tt.capacity,
                tt.language,
                tt.special_requirements,

                s.event_id,
                s.date,
                s.start_time,
                s.end_time,
                s.total_capacity,
                s.tickets_sold,
                s.is_sold_out,
                s.venue_id,
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

                -- Artist media fields
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

                -- Restaurant media fields
                restaurant_media.media_id as restaurant_media_id,
                restaurant_media.file_path as restaurant_media_file_path,
                restaurant_media.alt_text as restaurant_media_alt_text,

                -- Landmark fields
                l.landmark_id,
                l.name as landmark_name,
                l.name as landmark_title,
                l.short_description as landmark_short_description,
                l.landmark_slug,
                l.main_image_id,

                -- Landmark media fields
                landmark_media.media_id as landmark_media_id,
                landmark_media.file_path as landmark_media_file_path,
                landmark_media.alt_text as landmark_media_alt_text,

                -- Event category fields
                ec.event_id as event_category_id,
                ec.type as event_category_type,
                ec.title as event_category_title,
                ec.category_description as event_category_description,
                ec.slug as event_category_slug

            FROM TICKET_TYPE tt
            INNER JOIN SCHEDULE s ON tt.schedule_id = s.schedule_id
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
            LEFT JOIN MEDIA artist_media ON a.profile_image_id = artist_media.media_id
            LEFT JOIN RESTAURANT r ON s.restaurant_id = r.restaurant_id
            LEFT JOIN MEDIA restaurant_media ON r.main_image_id = restaurant_media.media_id
            LEFT JOIN LANDMARK l ON s.landmark_id = l.landmark_id
            LEFT JOIN MEDIA landmark_media ON l.main_image_id = landmark_media.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
        ";
    }

    public function getTicketTypeById(int $ticketTypeId): ?TicketType
    {
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery() . " WHERE tt.ticket_type_id = :ticket_type_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_type_id', $ticketTypeId, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }

            $ticketType = new TicketType();
            $ticketType->fromPDOData($row);
            return $ticketType;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket type by ID: " . $e->getMessage());
        }
    }

    public function getTicketTypesByScheduleId(int $scheduleId): array
    {
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery() . "
                WHERE tt.schedule_id = :schedule_id
                ORDER BY tt.ticket_type_id ASC
            ";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function (array $row): TicketType {
                $ticketType = new TicketType();
                $ticketType->fromPDOData($row);
                return $ticketType;
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket types by schedule ID: " . $e->getMessage());
        }
    }

    public function create(TicketType $ticketType): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO TICKET_TYPE (
                    schedule_id,
                    name,
                    price,
                    description,
                    reservation_fee,
                    min_age,
                    max_age,
                    min_quantity,
                    max_quantity,
                    capacity,
                    language,
                    special_requirements
                ) VALUES (
                    :schedule_id,
                    :name,
                    :price,
                    :description,
                    :reservation_fee,
                    :min_age,
                    :max_age,
                    :min_quantity,
                    :max_quantity,
                    :capacity,
                    :language,
                    :special_requirements
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':schedule_id', $ticketType->schedule?->schedule_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $ticketType->name);
            $stmt->bindValue(':price', $ticketType->price);
            $stmt->bindValue(':description', $ticketType->description);
            $stmt->bindValue(':reservation_fee', $ticketType->reservation_fee);
            $stmt->bindValue(':min_age', $ticketType->min_age, PDO::PARAM_INT);
            $stmt->bindValue(':max_age', $ticketType->max_age, PDO::PARAM_INT);
            $stmt->bindValue(':min_quantity', $ticketType->min_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':max_quantity', $ticketType->max_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':capacity', $ticketType->capacity, PDO::PARAM_INT);
            $stmt->bindValue(':language', $ticketType->language);
            $stmt->bindValue(':special_requirements', $ticketType->special_requirements);

            $result = $stmt->execute();
            if ($result) {
                $ticketType->ticket_type_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to create ticket type: " . $e->getMessage());
        }
    }

    public function update(TicketType $ticketType): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                UPDATE TICKET_TYPE SET
                    schedule_id = :schedule_id,
                    name = :name,
                    price = :price,
                    description = :description,
                    reservation_fee = :reservation_fee,
                    min_age = :min_age,
                    max_age = :max_age,
                    min_quantity = :min_quantity,
                    max_quantity = :max_quantity,
                    capacity = :capacity,
                    language = :language,
                    special_requirements = :special_requirements
                WHERE ticket_type_id = :ticket_type_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_type_id', $ticketType->ticket_type_id, PDO::PARAM_INT);
            $stmt->bindValue(':schedule_id', $ticketType->schedule?->schedule_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $ticketType->name);
            $stmt->bindValue(':price', $ticketType->price);
            $stmt->bindValue(':description', $ticketType->description);
            $stmt->bindValue(':reservation_fee', $ticketType->reservation_fee);
            $stmt->bindValue(':min_age', $ticketType->min_age, PDO::PARAM_INT);
            $stmt->bindValue(':max_age', $ticketType->max_age, PDO::PARAM_INT);
            $stmt->bindValue(':min_quantity', $ticketType->min_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':max_quantity', $ticketType->max_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':capacity', $ticketType->capacity, PDO::PARAM_INT);
            $stmt->bindValue(':language', $ticketType->language);
            $stmt->bindValue(':special_requirements', $ticketType->special_requirements);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to update ticket type: " . $e->getMessage());
        }
    }

    public function delete(int $ticketTypeId): bool
    {
        try {
            $pdo = $this->connect();

            $query = "DELETE FROM TICKET_TYPE WHERE ticket_type_id = :ticket_type_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_type_id', $ticketTypeId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to delete ticket type: " . $e->getMessage());
        }
    }
}