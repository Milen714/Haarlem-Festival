<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IScheduleRepository;
use PDO;
use PDOException;

class ScheduleRepository extends Repository implements IScheduleRepository
{
    /**
     * Gets all the schedules for a specific event ( dont know it it works for everyone but for jazz t does)
     */
    public function getScheduleByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    s.schedule_id,
                    s.date,
                    s.start_time,
                    s.end_time,
                    s.total_capacity,
                    s.tickets_sold,
                    s.is_sold_out,
                    v.venue_id,
                    v.name as venue_name,
                    v.street_address as venue_address,
                    a.artist_id,
                    a.name as artist_name,
                    a.slug as artist_slug
                FROM SCHEDULE s
                LEFT JOIN VENUE v ON s.venue_id = v.venue_id
                LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
                WHERE s.event_id = :event_id
                ORDER BY s.date ASC, s.start_time ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error fetching schedule by event: " . $e->getMessage());
        }
    }
}