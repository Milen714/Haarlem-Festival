<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TicketScheme;
use App\Models\TicketType;
use App\Repositories\Interfaces\ITicketRepository;
use App\Models\History\TicketSelectionDTO;
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
                tt.description,
                tt.min_age,
                tt.max_age,
                tt.min_quantity,
                tt.max_quantity,
                tt.tickets_sold,
                tt.is_sold_out,
                tt.capacity,
                tt.special_requirements,
                tt.tickets_sold,
                tt.is_sold_out,

                -- Ticket scheme fields
                ts.ticket_scheme_id,
                ts.name as name,
                ts.scheme_enum,
                ts.price,
                ts.fee,
                ts.ticket_language,

                s.event_id,
                s.date,
                s.start_time,
                s.end_time,
                s.total_capacity,
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
                v.venue_image_id as venue_image_id,
                 -- Venue media fields
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
            LEFT JOIN TICKET_SCHEME ts ON tt.scheme_id = ts.ticket_scheme_id
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN MEDIA venue_media ON v.venue_image_id = venue_media.media_id
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

    // method to get ticket types by an array of scheme enums, used for filtering in the schedule list specific for day passes for jazz and dance events
    public function getTicketTypesBySchemeEnums(array $schemeEnums): array
    {
        if (empty($schemeEnums)) {
            return [];
        }

        try {
            $pdo = $this->connect();
            $placeholders = implode(',', array_fill(0, count($schemeEnums), '?'));
            $query = $this->getBaseQuery() . "
                WHERE ts.scheme_enum IN ($placeholders)
                ORDER BY tt.ticket_type_id ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute(array_values($schemeEnums));

            return array_map(function (array $row): TicketType {
                $ticketType = new TicketType();
                $ticketType->fromPDOData($row);
                return $ticketType;
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket types by scheme enums: " . $e->getMessage());
        }
    }

    public function getTicketTypesByScheduleIds(array $scheduleIds): array
    {
        if (empty($scheduleIds)) {
            return [];
        }

        try {
            $pdo = $this->connect();
            $placeholders = implode(',', array_fill(0, count($scheduleIds), '?'));
            $query = $this->getBaseQuery() . "
                WHERE tt.schedule_id IN ($placeholders)
                ORDER BY tt.schedule_id ASC, tt.ticket_type_id ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute(array_values($scheduleIds));

            $grouped = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ticketType = new TicketType();
                $ticketType->fromPDOData($row);
                $grouped[(int)$row['schedule_id']][] = $ticketType;
            }

            return $grouped;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket types by schedule IDs: " . $e->getMessage());
        }
    }

    public function create(TicketType $ticketType): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO TICKET_TYPE (
                    schedule_id,
                    scheme_id,
                    description,
                    min_age,
                    max_age,
                    min_quantity,
                    max_quantity,
                    tickets_sold,
                    is_sold_out,
                    capacity,
                    special_requirements
                ) VALUES (
                    :schedule_id,
                    :scheme_id,
                    :description,
                    :min_age,
                    :max_age,
                    :min_quantity,
                    :max_quantity,
                    :tickets_sold,
                    :is_sold_out,
                    :capacity,
                    :special_requirements
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':schedule_id', $ticketType->schedule?->schedule_id, PDO::PARAM_INT);
            $stmt->bindValue(':scheme_id', $ticketType->ticket_scheme?->ticket_scheme_id, PDO::PARAM_INT);
            $stmt->bindValue(':description', $ticketType->description);
            $stmt->bindValue(':min_age', $ticketType->min_age, PDO::PARAM_INT);
            $stmt->bindValue(':max_age', $ticketType->max_age, PDO::PARAM_INT);
            $stmt->bindValue(':min_quantity', $ticketType->min_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':max_quantity', $ticketType->max_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':tickets_sold', $ticketType->tickets_sold, PDO::PARAM_INT);
            $stmt->bindValue(':is_sold_out', $ticketType->is_sold_out, PDO::PARAM_BOOL);
            $stmt->bindValue(':capacity', $ticketType->capacity, PDO::PARAM_INT);
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
                    scheme_id = :scheme_id,
                    description = :description,
                    min_age = :min_age,
                    max_age = :max_age,
                    min_quantity = :min_quantity,
                    max_quantity = :max_quantity,
                    tickets_sold = :tickets_sold,
                    is_sold_out = :is_sold_out,
                    capacity = :capacity,
                    special_requirements = :special_requirements
                WHERE ticket_type_id = :ticket_type_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_type_id', $ticketType->ticket_type_id, PDO::PARAM_INT);
            $stmt->bindValue(':schedule_id', $ticketType->schedule?->schedule_id, PDO::PARAM_INT);
            $stmt->bindValue(':scheme_id', $ticketType->ticket_scheme?->ticket_scheme_id, PDO::PARAM_INT);
            $stmt->bindValue(':description', $ticketType->description);
            $stmt->bindValue(':min_age', $ticketType->min_age, PDO::PARAM_INT);
            $stmt->bindValue(':max_age', $ticketType->max_age, PDO::PARAM_INT);
            $stmt->bindValue(':min_quantity', $ticketType->min_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':max_quantity', $ticketType->max_quantity, PDO::PARAM_INT);
            $stmt->bindValue(':tickets_sold', $ticketType->tickets_sold, PDO::PARAM_INT);
            $stmt->bindValue(':is_sold_out', $ticketType->is_sold_out, PDO::PARAM_BOOL);
            $stmt->bindValue(':capacity', $ticketType->capacity, PDO::PARAM_INT);
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

    public function getTicketSchemeById(int $ticketSchemeId): ?TicketScheme
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT
                    ticket_scheme_id,
                    name,
                    scheme_enum,
                    price,
                    fee,
                    ticket_language
                FROM TICKET_SCHEME
                WHERE ticket_scheme_id = :ticket_scheme_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_scheme_id', $ticketSchemeId, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }

            return (new TicketScheme())->fromPDOData($row);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket scheme by ID: " . $e->getMessage());
        }
    }

    public function getAllTicketSchemes(): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT
                    ticket_scheme_id,
                    name,
                    scheme_enum,
                    price,
                    fee,
                    ticket_language
                FROM TICKET_SCHEME
                ORDER BY ticket_scheme_id ASC
            ";

            $stmt = $pdo->query($query);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function (array $row): TicketScheme {
                return (new TicketScheme())->fromPDOData($row);
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket schemes: " . $e->getMessage());
        }
    }

    public function getTicketSchemeUsageCounts(): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT
                    scheme_id,
                    COUNT(*) AS usage_count
                FROM TICKET_TYPE
                WHERE scheme_id IS NOT NULL
                GROUP BY scheme_id
            ";

            $stmt = $pdo->query($query);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $usageCounts = [];
            foreach ($rows as $row) {
                $usageCounts[(int)$row['scheme_id']] = (int)$row['usage_count'];
            }

            return $usageCounts;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching ticket scheme usage counts: " . $e->getMessage());
        }
    }

    public function countTicketTypesBySchemeId(int $ticketSchemeId): int
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT COUNT(*)
                FROM TICKET_TYPE
                WHERE scheme_id = :ticket_scheme_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_scheme_id', $ticketSchemeId, PDO::PARAM_INT);
            $stmt->execute();

            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new \RuntimeException("Error counting ticket types by scheme ID: " . $e->getMessage());
        }
    }

    // Fresh DB read so the count is never stale
    public function getAvailableCapacity(int $ticketTypeId): int
    {
        try {
            $pdo  = $this->connect();
            $stmt = $pdo->prepare(
                "SELECT GREATEST(0, capacity - tickets_sold) AS available
                 FROM TICKET_TYPE
                 WHERE ticket_type_id = :id AND is_sold_out = 0"
            );
            $stmt->bindValue(':id', $ticketTypeId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['available'] : 0;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error checking capacity: " . $e->getMessage());
        }
    }

    // Increments tickets_sold atomically. Sets is_sold_out when capacity is reached. Returns false if no seats left.
    public function atomicIncrementTicketsSold(int $ticketTypeId, int $quantity): bool
    {
        try {
            $pdo  = $this->connect();
            $stmt = $pdo->prepare(
                "UPDATE TICKET_TYPE
                 SET
                     tickets_sold = tickets_sold + :qty,
                     is_sold_out  = CASE
                                        WHEN (tickets_sold + :qty2) >= capacity THEN 1
                                        ELSE 0
                                    END
                 WHERE ticket_type_id = :id
                   AND is_sold_out    = 0
                   AND (tickets_sold  + :qty3) <= capacity"
            );
            $stmt->bindValue(':qty',  $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':qty2', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':qty3', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':id',   $ticketTypeId, PDO::PARAM_INT);
            $stmt->execute();
            // 0 rows = WHERE guard failed, no seats left
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error incrementing tickets sold: " . $e->getMessage());
        }
    }

    // Decrements tickets_sold atomically. Clears is_sold_out if back below capacity. Returns false if nothing to decrement.
    public function atomicDecrementTicketsSold(int $ticketTypeId, int $quantity): bool
    {
        try {
            $pdo  = $this->connect();
            $stmt = $pdo->prepare(
                "UPDATE TICKET_TYPE
                 SET
                     tickets_sold = GREATEST(0, tickets_sold - :qty),
                     is_sold_out  = CASE
                                        WHEN (tickets_sold - :qty2) < capacity THEN 0
                                        ELSE is_sold_out
                                    END
                 WHERE ticket_type_id = :id
                   AND tickets_sold   >= :qty3"
            );
            $stmt->bindValue(':qty',  $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':qty2', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':qty3', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':id',   $ticketTypeId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error decrementing tickets sold: " . $e->getMessage());
        }
    }

    // Reserves seats for multiple ticket types in one transaction. All succeed or none do.
    public function reserveMultiple(array $items): bool
    {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            foreach ($items as $item) {
                $ticketTypeId = (int)($item['ticket_type_id'] ?? 0);
                $quantity     = (int)($item['quantity'] ?? 0);
                if ($ticketTypeId <= 0 || $quantity <= 0) {
                    continue;
                }

                $stmt = $pdo->prepare(
                    "UPDATE TICKET_TYPE
                     SET
                         tickets_sold = tickets_sold + :qty,
                         is_sold_out  = CASE
                                            WHEN (tickets_sold + :qty2) >= capacity THEN 1
                                            ELSE 0
                                        END
                     WHERE ticket_type_id = :id
                       AND is_sold_out    = 0
                       AND (tickets_sold  + :qty3) <= capacity"
                );
                $stmt->bindValue(':qty',  $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':qty2', $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':qty3', $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':id',   $ticketTypeId, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    $pdo->rollBack();
                    return false;
                }
            }
            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw new \RuntimeException("Error reserving multiple tickets: " . $e->getMessage());
        }
    }

    // Releases seats for multiple ticket types in one transaction.
    public function releaseMultiple(array $items): void
    {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            foreach ($items as $item) {
                $ticketTypeId = (int)($item['ticket_type_id'] ?? 0);
                $quantity     = (int)($item['quantity'] ?? 0);
                if ($ticketTypeId <= 0 || $quantity <= 0) {
                    continue;
                }

                $stmt = $pdo->prepare(
                    "UPDATE TICKET_TYPE
                     SET
                         tickets_sold = GREATEST(0, tickets_sold - :qty),
                         is_sold_out  = CASE
                                            WHEN (tickets_sold - :qty2) < capacity THEN 0
                                            ELSE is_sold_out
                                        END
                     WHERE ticket_type_id = :id
                       AND tickets_sold   >= :qty3"
                );
                $stmt->bindValue(':qty',  $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':qty2', $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':qty3', $quantity, PDO::PARAM_INT);
                $stmt->bindValue(':id',   $ticketTypeId, PDO::PARAM_INT);
                $stmt->execute();
            }
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw new \RuntimeException("Error releasing multiple tickets: " . $e->getMessage());
        }
    }

    // Total capacity already allocated for a schedule. Pass excludeTicketTypeId when updating an existing type.
    public function getTotalAllocatedCapacityForSchedule(int $scheduleId, ?int $excludeTicketTypeId = null): int
    {
        try {
            $pdo  = $this->connect();
            $stmt = $pdo->prepare(
                "SELECT COALESCE(SUM(capacity), 0) AS total
                 FROM TICKET_TYPE
                 WHERE schedule_id = :schedule_id
                   AND (:exclude_id IS NULL OR ticket_type_id != :exclude_id2)"
            );
            $stmt->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
            $stmt->bindValue(':exclude_id',  $excludeTicketTypeId, $excludeTicketTypeId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':exclude_id2', $excludeTicketTypeId, $excludeTicketTypeId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['total'] ?? 0);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error getting total allocated capacity: " . $e->getMessage());
        }
    }

    // Returns the venue's capacity for a schedule, or null if no venue is linked.
    public function getVenueCapacityForSchedule(int $scheduleId): ?int
    {
        try {
            $pdo  = $this->connect();
            $stmt = $pdo->prepare(
                "SELECT v.capacity AS venue_capacity
                 FROM SCHEDULE s
                 LEFT JOIN VENUE v ON s.venue_id = v.venue_id
                 WHERE s.schedule_id = :schedule_id"
            );
            $stmt->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || $row['venue_capacity'] === null) {
                return null;
            }
            return (int)$row['venue_capacity'];
        } catch (PDOException $e) {
            throw new \RuntimeException("Error getting venue capacity for schedule: " . $e->getMessage());
        }
    }

    public function createTicketScheme(TicketScheme $ticketScheme): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO TICKET_SCHEME (
                    name,
                    scheme_enum,
                    price,
                    fee,
                    ticket_language
                ) VALUES (
                    :name,
                    :scheme_enum,
                    :price,
                    :fee,
                    :ticket_language
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name', $ticketScheme->name);
            $stmt->bindValue(':scheme_enum', $ticketScheme->scheme_enum?->value);
            $stmt->bindValue(':price', $ticketScheme->price);
            $stmt->bindValue(':fee', $ticketScheme->fee);
            $stmt->bindValue(':ticket_language', $ticketScheme->ticket_language?->value);

            $result = $stmt->execute();
            if ($result) {
                $ticketScheme->ticket_scheme_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to create ticket scheme: " . $e->getMessage());
        }
    }

    public function updateTicketScheme(TicketScheme $ticketScheme): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                UPDATE TICKET_SCHEME SET
                    name = :name,
                    scheme_enum = :scheme_enum,
                    price = :price,
                    fee = :fee,
                    ticket_language = :ticket_language
                WHERE ticket_scheme_id = :ticket_scheme_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_scheme_id', $ticketScheme->ticket_scheme_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $ticketScheme->name);
            $stmt->bindValue(':scheme_enum', $ticketScheme->scheme_enum?->value);
            $stmt->bindValue(':price', $ticketScheme->price);
            $stmt->bindValue(':fee', $ticketScheme->fee);
            $stmt->bindValue(':ticket_language', $ticketScheme->ticket_language?->value);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to update ticket scheme: " . $e->getMessage());
        }
    }

    public function deleteTicketScheme(int $ticketSchemeId): bool
    {
        try {
            $pdo = $this->connect();

            $query = "DELETE FROM TICKET_SCHEME WHERE ticket_scheme_id = :ticket_scheme_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':ticket_scheme_id', $ticketSchemeId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to delete ticket scheme: " . $e->getMessage());
        }
    }

    public function getTicketTypeFromSelection(TicketSelectionDTO $selectionDto): ?TicketType
    {
        $sql = "SELECT tt.ticket_type_id 
                FROM TICKET_TYPE tt
                INNER JOIN SCHEDULE s ON tt.schedule_id = s.schedule_id
                INNER JOIN TICKET_SCHEME ts ON tt.scheme_id = ts.ticket_scheme_id
                WHERE s.date = :date 
                  AND s.start_time = :time 
                  AND ts.ticket_language = :language 
                  AND ts.scheme_enum = :schemeEnum
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        // bindValue vincula el dato real de la variable en este preciso instante
        $stmt->bindValue(':date', $selectionDto->date, \PDO::PARAM_STR);
        $stmt->bindValue(':time', $selectionDto->time, \PDO::PARAM_STR);
        $stmt->bindValue(':language', $selectionDto->language, \PDO::PARAM_STR);
        $stmt->bindValue(':schemeEnum', $selectionDto->ticketSchemeEnum?->value, \PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? (int)$result['ticket_type_id'] : null;
    }
}
