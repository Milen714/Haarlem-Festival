<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IVenueRepository;
use PDO;
use PDOException;

class VenueRepository extends Repository implements IVenueRepository
{
    /**
     * Gets all the venues that are used by a specific(all 5) event
     */
    public function getVenuesByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT DISTINCT
                    v.venue_id,
                    v.name,
                    v.street_address,
                    v.city,
                    v.postal_code,
                    v.country,
                    v.description_html,
                    v.capacity,
                    v.phone,
                    v.email,
                    m.file_path as image_path,
                    m.alt_text as image_alt
                FROM VENUE v
                INNER JOIN SCHEDULE s ON v.venue_id = s.venue_id
                LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
                WHERE s.event_id = :event_id
                ORDER BY v.name ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error fetching venues by event: " . $e->getMessage());
        }
    }

    /**
     * Gets venue by ID
     */
    public function getVenueById(int $venueId): ?object
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    v.*,
                    m.file_path as image_path,
                    m.alt_text as image_alt
                FROM VENUE v
                LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
                WHERE v.venue_id = :venue_id
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':venue_id', $venueId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ?: null;
        } catch (PDOException $e) {
            die("Error fetching venue by ID: " . $e->getMessage());
        }
    }
}