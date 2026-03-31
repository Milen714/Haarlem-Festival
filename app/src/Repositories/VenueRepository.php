<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Models\Venue;
use PDO;
use PDOException;

class VenueRepository extends Repository implements IVenueRepository
{
    public function getVenuesByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT DISTINCT
                    v.*,
                    m.file_path as image_path,
                    m.alt_text as image_alt,
                    ec.event_id as event_category_id,
                    ec.title as event_category_title,
                    ec.type as event_category_type,
                    ec.slug as event_category_slug
                FROM VENUE v
                INNER JOIN SCHEDULE s ON v.venue_id = s.venue_id
                LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
                LEFT JOIN EVENT_CATEGORIES ec ON v.event_id = ec.event_id
                WHERE s.event_id = :event_id
                ORDER BY v.name ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $venues = [];
            foreach ($results as $row) {
                $venue = new Venue();
                $venue->fromPDOData($row);
                $venues[] = $venue;
            }

            return $venues;
        } catch (PDOException $e) {
            throw new PDOException("Failed to fetch venues for event {$eventId}", 0, $e);
        }
    }

    public function getVenueById(int $venueId): ?Venue
    {
        try {
            $pdo = $this->connect();

            $query = "
                SELECT
                    v.*,
                    m.file_path as image_path,
                    m.alt_text as image_alt,
                    ec.event_id as event_category_id,
                    ec.title as event_category_title,
                    ec.type as event_category_type,
                    ec.slug as event_category_slug
                FROM VENUE v
                LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
                LEFT JOIN EVENT_CATEGORIES ec ON v.event_id = ec.event_id
                WHERE v.venue_id = :venue_id
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':venue_id', $venueId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            $venue = new Venue();
            $venue->fromPDOData($result);
            return $venue;
        } catch (PDOException $e) {
            throw new PDOException("Failed to fetch venue by ID: {$venueId}", 0, $e);
        }
    }

    /**
     * Hard-deletes a venue row from the database.
     * Note: the VENUE table has no deleted_at column, so this is a permanent removal.
     * Ensure no active schedules reference this venue before calling.
     *
     * @param int $venueId  The primary key of the venue to permanently delete.
     *
     * @return bool  True if the DELETE executed successfully.
     *
     * @throws PDOException  If the database query fails.
     */
    public function delete(int $venueId): bool
    {
        try {
            $pdo = $this->connect();

            // Hard delete since VENUE table doesn't have deleted_at column
            $query = "DELETE FROM VENUE WHERE venue_id = :venue_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':venue_id', $venueId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Failed to delete venue", 0, $e);
        }
    }

    public function getAllVenues(): array
    {
        try {
            $pdo = $this->connect();

            $query = "
            SELECT
                v.*,
                v.venue_image_id,
                m.media_id,
                m.file_path as image_path,
                m.alt_text as image_alt,
                COUNT(DISTINCT s.schedule_id) as event_count,
                ec.event_id as event_category_id,
                ec.title as event_category_title,
                ec.type as event_category_type,
                ec.slug as event_category_slug
            FROM VENUE v
            LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
            LEFT JOIN SCHEDULE s ON v.venue_id = s.venue_id
            LEFT JOIN EVENT_CATEGORIES ec ON v.event_id = ec.event_id
            GROUP BY v.venue_id
            ORDER BY v.name ASC
        ";

            $stmt = $pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $venues = [];
            foreach ($results as $row) {
                $venue = new Venue();
                $venue->fromPDOData($row);
                $venues[] = $venue;
            }


        return $venues;
    } catch (PDOException $e) {
        throw new PDOException("Failed to fetch venues: " . $e->getMessage(), 0, $e);
    }
    }

    /**
     * Inserts a new venue record and writes the generated primary key back onto the Venue object.
     * Call Venue::createFromPostData() before this to build the object from form input.
     *
     * @param Venue $venue  The venue to persist; venue_id will be set to the new auto-increment value on success.
     *
     * @return bool  True if the INSERT succeeded.
     *
     * @throws PDOException  If the database query fails.
     */
    public function create(Venue $venue): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO VENUE (
                    name,
                    street_address,
                    city,
                    postal_code,
                    country,
                    description_html,
                    capacity,
                    phone,
                    email,
                    venue_image_id
                ) VALUES (
                    :name,
                    :street_address,
                    :city,
                    :postal_code,
                    :country,
                    :description_html,
                    :capacity,
                    :phone,
                    :email,
                    :venue_image_id
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name', $venue->name);
            $stmt->bindValue(':street_address', $venue->street_address);
            $stmt->bindValue(':city', $venue->city);
            $stmt->bindValue(':postal_code', $venue->postal_code);
            $stmt->bindValue(':country', $venue->country);
            $stmt->bindValue(':description_html', $venue->description_html);
            $stmt->bindValue(':capacity', $venue->capacity, PDO::PARAM_INT);
            $stmt->bindValue(':phone', $venue->phone);
            $stmt->bindValue(':email', $venue->email);

            $venueImageId = $venue->venue_image?->media_id ?? null;
            $stmt->bindValue(':venue_image_id', $venueImageId, PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                $venue->venue_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error creating venue: " . $e->getMessage());
            throw new PDOException("Failed to create venue", 0, $e);
        }
    }

    public function update(Venue $venue): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                UPDATE VENUE SET
                    name = :name,
                    street_address = :street_address,
                    city = :city,
                    postal_code = :postal_code,
                    country = :country,
                    description_html = :description_html,
                    capacity = :capacity,
                    phone = :phone,
                    email = :email,
                    venue_image_id = :venue_image_id
                WHERE venue_id = :venue_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':venue_id', $venue->venue_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $venue->name);
            $stmt->bindValue(':street_address', $venue->street_address);
            $stmt->bindValue(':city', $venue->city);
            $stmt->bindValue(':postal_code', $venue->postal_code);
            $stmt->bindValue(':country', $venue->country);
            $stmt->bindValue(':description_html', $venue->description_html);
            $stmt->bindValue(':capacity', $venue->capacity, PDO::PARAM_INT);
            $stmt->bindValue(':phone', $venue->phone);
            $stmt->bindValue(':email', $venue->email);

            $venueImageId = $venue->venue_image?->media_id ?? null;
            $stmt->bindValue(':venue_image_id', $venueImageId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating venue: " . $e->getMessage());
            throw new PDOException("Failed to update venue", 0, $e);
        }
    }
}
