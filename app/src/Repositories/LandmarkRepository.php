<?php

namespace App\Repositories;

use App\Models\Landmark;
use App\Framework\Repository;
use PDO;

class LandmarkRepository extends Repository 
{
    public function __construct() {
        $this->pdo = $this->connect();
    }

    /** @return Landmark[] */
    public function getAll(): array
    {
        $sql = "SELECT * FROM LANDMARK ORDER BY display_order ASC";
        
        $stmt = $this->pdo->query($sql); 
        
        $landmarks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $landmark = new Landmark();
            $landmark->fromPDOData($row);
            $landmarks[] = $landmark;
        }

        return $landmarks;
    }


public function addMediaToGallery(int $galleryId, int $mediaId): bool
{
    $sql = "INSERT INTO GALLERY_MEDIA (gallery_id, media_id) VALUES (:gallery_id, :media_id)";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'gallery_id' => $galleryId,
        'media_id' => $mediaId
    ]);
}

    public function getBySlug(string $slug): ?Landmark
    {
        $sql = "SELECT landmark.*, 
                       media.media_id, media.file_path, media.alt_text, 
                       gm.display_order
                FROM LANDMARK landmark
                LEFT JOIN GALLERY_MEDIA gm ON landmark.gallery_id = gm.gallery_id
                LEFT JOIN MEDIA media ON gm.media_id = media.media_id
                WHERE landmark.landmark_slug = :slug
                ORDER BY gm.display_order ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$rows) return null;

        $landmark = new Landmark();
        $landmark->fromPDOData($rows[0]);
        
        if (!empty($rows[0]['gallery_id'])) {
            $gallery = new \App\Models\Gallery();
            $gallery->gallery_id = $rows[0]['gallery_id'];

            foreach ($rows as $row) {
                if (!empty($row['media_id'])) {
                    $media = new \App\Models\Media();
                    $media->fromPDOData([
                        'media_id'  => $row['media_id'],
                        'file_path' => $row['file_path'],
                        'alt_text'  => $row['alt_text'] ?? ''
                    ]);
                    
                    $galleryMedia = new \App\Models\GalleryMedia();
                    $galleryMedia->media = $media;
                    $galleryMedia->order = $row['display_order'] ?? 0;
                    
                    $gallery->addGalleryMedia($galleryMedia);
                }
            }
            $landmark->gallery = $gallery;
        }
        
        return $landmark;
    }

    public function getById(int $id): ?Landmark
    {
        $sql = "SELECT landmark.*, 
                       media.media_id, media.file_path, media.alt_text, 
                       gm.display_order
                FROM LANDMARK landmark
                LEFT JOIN GALLERY_MEDIA gm ON landmark.gallery_id = gm.gallery_id
                LEFT JOIN MEDIA media ON gm.media_id = media.media_id
                WHERE landmark.landmark_id = :id
                ORDER BY gm.display_order ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$rows) {
            return null;
        }
        
        $landmark = new Landmark();
        $landmark->fromPDOData($rows[0]);

        if (!empty($rows[0]['gallery_id'])) {
            $gallery = new \App\Models\Gallery();
            $gallery->gallery_id = $rows[0]['gallery_id'];

            foreach ($rows as $row) {
                if (!empty($row['media_id'])) {
                    $media = new \App\Models\Media();
                    $media->fromPDOData([
                        'media_id'  => $row['media_id'],
                        'file_path' => $row['file_path'],
                        'alt_text'  => $row['alt_text'] ?? ''
                    ]);
                    
                    $galleryMedia = new \App\Models\GalleryMedia();
                    $galleryMedia->media = $media;
                    $galleryMedia->order = $row['display_order'] ?? 0;
                    
                    $gallery->addGalleryMedia($galleryMedia);
                }
            }
            $landmark->gallery = $gallery;
        }
        
        return $landmark;
    }

    public function insert(Landmark $landmark): Landmark
    {
        $sql = "INSERT INTO LANDMARK (
                    event_id, name, short_description, landmark_slug, 
                    intro_title, intro_content, why_visit_title, why_visit_content, 
                    detail_history_title, detail_history_content, display_order
                ) VALUES (
                    :event_id, :name, :short_description, :landmark_slug, 
                    :intro_title, :intro_content, :why_visit_title, :why_visit_content, 
                    :detail_history_title, :detail_history_content, :display_order
                )";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':event_id', $landmark->event_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $landmark->name, PDO::PARAM_STR);
        $stmt->bindParam(':short_description', $landmark->short_description, PDO::PARAM_STR);
        $stmt->bindParam(':landmark_slug', $landmark->landmark_slug, PDO::PARAM_STR);
        $stmt->bindParam(':intro_title', $landmark->intro_title, PDO::PARAM_STR);
        $stmt->bindParam(':intro_content', $landmark->intro_content, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_title', $landmark->why_visit_title, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_content', $landmark->why_visit_content, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_title', $landmark->detail_history_title, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_content', $landmark->detail_history_content, PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $landmark->display_order, PDO::PARAM_INT);

        $stmt->execute();

        $landmark->landmark_id = (int)$this->pdo->lastInsertId(); //to get the new id
        
        return $landmark;
    }

    public function update(Landmark $landmark): Landmark
    {
        $sql = "UPDATE LANDMARK SET 
                    event_id = :event_id,
                    name = :name,
                    short_description = :short_description,
                    landmark_slug = :landmark_slug,
                    intro_title = :intro_title,
                    intro_content = :intro_content,
                    why_visit_title = :why_visit_title,
                    why_visit_content = :why_visit_content,
                    detail_history_title = :detail_history_title,
                    detail_history_content = :detail_history_content,
                    display_order = :display_order
                WHERE landmark_id = :landmark_id";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':landmark_id', $landmark->landmark_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_id', $landmark->event_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $landmark->name, PDO::PARAM_STR);
        $stmt->bindParam(':short_description', $landmark->short_description, PDO::PARAM_STR);
        $stmt->bindParam(':landmark_slug', $landmark->landmark_slug, PDO::PARAM_STR);
        $stmt->bindParam(':intro_title', $landmark->intro_title, PDO::PARAM_STR);
        $stmt->bindParam(':intro_content', $landmark->intro_content, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_title', $landmark->why_visit_title, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_content', $landmark->why_visit_content, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_title', $landmark->detail_history_title, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_content', $landmark->detail_history_content, PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $landmark->display_order, PDO::PARAM_INT);

        $stmt->execute();

        return $landmark;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM LANDMARK WHERE landmark_id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}