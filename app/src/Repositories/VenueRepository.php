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
                    v.venue_image_id,
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
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $venues = [];
            foreach ($results as $row) {
                $venue = new Venue();
                $venue->fromPDOData($row);
                $venues[] = $venue;
            }
            
            return $venues;
        } catch (PDOException $e) {
            error_log("Error fetching venues by event: " . $e->getMessage());
            throw new \RuntimeException("Failed to fetch venues for event {$eventId}", 0, $e);
        }
    }

    public function getVenueById(int $venueId): ?Venue
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    v.*,
                    v.venue_image_id,
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
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return null;
            }
            
            $venue = new Venue();
            $venue->fromPDOData($result);
            return $venue;
        } catch (PDOException $e) {
            error_log("Error fetching venue by ID: " . $e->getMessage());
            throw new \RuntimeException("Failed to fetch venue by ID: {$venueId}", 0, $e);
        }
    }

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
            error_log("Error deleting venue: " . $e->getMessage());
            throw new \RuntimeException("Failed to delete venue", 0, $e);
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
                COUNT(DISTINCT s.schedule_id) as event_count
            FROM VENUE v
            LEFT JOIN MEDIA m ON v.venue_image_id = m.media_id
            LEFT JOIN SCHEDULE s ON v.venue_id = s.venue_id
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
        error_log("Error fetching all venues: " . $e->getMessage());
        throw new \RuntimeException("Failed to fetch venues", 0, $e);
    }
}

    /**
     * Create new venue
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
            throw new \RuntimeException("Failed to create venue", 0, $e);
        }
    }

    /**
     * Update existing venue
     */
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
            throw new \RuntimeException("Failed to update venue", 0, $e);
        }
    }
}