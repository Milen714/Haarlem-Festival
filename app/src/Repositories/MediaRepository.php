<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IMediaRepository;
use App\Framework\Repository;
use App\Models\Media;
use App\Models\GalleryMedia;
use App\Models\Gallery;
use PDO;
use PDOException;

class MediaRepository extends Repository implements IMediaRepository
{
    public function getMediaById(int $id): Media
    {
        try {
            $pdo = $this->connect();
            $query = 'SELECT media_id, file_path, alt_text FROM MEDIA WHERE media_id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            $mediaModel = new Media();
            if ($media) {
                $mediaModel->fromPDOData($media);
            }
            return $mediaModel;
        } catch (PDOException $e) {
            die("Error fetching media: " . $e->getMessage());
        }
    }

    public function updateMedia(Media $media): bool
    {
        try {
            $pdo = $this->connect();

            if ($media->media_id !== null && ($media->file_path === null || $media->file_path === '')) {
                $existingMedia = $this->getMediaById($media->media_id);
                $media->file_path = $existingMedia->file_path;
            }

            $query = 'UPDATE MEDIA 
                      SET file_path = :file_path, alt_text = :alt_text
                      WHERE media_id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $media->media_id);
            $stmt->bindParam(':file_path', $media->file_path);
            $stmt->bindParam(':alt_text', $media->alt_text);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error updating media: " . $e->getMessage());
        }
    }

    public function createMedia(Media $media): bool
    {
        try {
            $pdo = $this->connect();
            
            $query = 'INSERT INTO MEDIA (file_path, alt_text) 
                      VALUES (:file_path, :alt_text)';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':file_path', $media->file_path);
            $stmt->bindParam(':alt_text', $media->alt_text);
            $executed = $stmt->execute();

            if ($executed) {
                $media->media_id = (int)$pdo->lastInsertId();
            }

            return $executed;
        } catch (PDOException $e) {
            die("Error creating media: " . $e->getMessage());
        }
    }

    public function deleteMedia(int $mediaId): bool
    {
        try {
            $pdo = $this->connect();
            $query = 'DELETE FROM MEDIA WHERE media_id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $mediaId);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error deleting media: " . $e->getMessage());
        }
    }

    public function getGalleryById(int $galleryId): Gallery
    {
        try {
            $pdo = $this->connect();
            $query = 'SELECT 
                g.gallery_id, g.title AS gallery_title, g.created_at, 
                gm.gallery_id as gm_gallery_id, gm.media_id, gm.display_order
                FROM GALLERY g
                LEFT JOIN GALLERY_MEDIA as gm ON g.gallery_id = gm.gallery_id
                WHERE g.gallery_id = :galleryId
                ORDER BY gm.display_order ASC';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':galleryId', $galleryId);
            $stmt->execute();
            $galleryData = $stmt->fetch(PDO::FETCH_ASSOC);
            $gallery = new Gallery();
            if ($galleryData) {
                $gallery->fromPDOData($galleryData);
                do {
                    if ($galleryData['gm_gallery_id']) {
                        $media = $this->getMediaById((int)$galleryData['media_id']);
                        $galleryMedia = new GalleryMedia();
                        $galleryMedia->fromPDOData($galleryData);
                        $galleryMedia->media = $media;
                        $gallery->addGalleryMedia($galleryMedia);
                    }
                } while ($galleryData = $stmt->fetch(PDO::FETCH_ASSOC));
            }
            return $gallery;
        } catch (PDOException $e) {
            die("Error fetching gallery: " . $e->getMessage());
        }
    }
}