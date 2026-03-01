<?php

namespace App\Repositories;


use App\Models\History\Landmark;
use App\Framework\Repository;
use App\Repositories\Interfaces\ILandmarkRepository;
use PDO;
use PDOException;


class LandmarkRepository extends Repository implements ILandmarkRepository
{
    public function getLandmarkById(int $landmarkId): ?Landmark
    {
        try {
            $pdo = $this->connect();
            $query = '
            SELECT l.*,
                m.media_id,
                m.file_path as image_path,
                m.alt_text as image_alt, 
                ec.event_id as event_category_id,
                ec.title as event_category_title,
                ec.type as event_category_type,
                ec.slug as event_category_slug
            FROM LANDMARK l
            LEFT JOIN MEDIA m ON l.main_image_id = m.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON l.event_category_id = ec.event_category_id
            WHERE l.landmark_id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $landmarkId);
            $stmt->execute();
            $landmarkData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($landmarkData) {
                $landmark = new Landmark();
                $landmark->fromPDOData($landmarkData);
                return $landmark;
            }
            return null;
        } catch (PDOException $e) {
            die("Error fetching landmark: " . $e->getMessage());
        }
    }
    public function getAllLandmarks(): array
    {
        try {
            $pdo = $this->connect();
            $query = '
            SELECT l.*,
                m.media_id,
                m.file_path as landmark_image_path,
                m.alt_text as landmark_image_alt, 
                ec.event_id as event_category_id,
                ec.title as event_category_title,
                ec.type as event_category_type,
                ec.slug as event_category_slug
            FROM LANDMARK l
            LEFT JOIN MEDIA m ON l.main_image_id = m.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON l.event_id = ec.event_id
            ORDER BY l.display_order ASC';
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $landmarkDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $landmarks = [];
            foreach ($landmarkDataArray as $landmarkData) {
                $landmark = new Landmark();
                $landmark->fromPDOData($landmarkData);
                $landmarks[] = $landmark;
            }
            return $landmarks;
        } catch (PDOException $e) {
            die("Error fetching landmarks: " . $e->getMessage());
        }
    }

}