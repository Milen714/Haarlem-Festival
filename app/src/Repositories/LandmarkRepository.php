<?php

namespace App\Repositories;

use App\Models\Landmark;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\GalleryMedia;
use App\Framework\Repository;
use App\Repositories\Interfaces\ILandmarkRepository;
use PDO;

class LandmarkRepository extends Repository implements ILandmarkRepository
{
    public function __construct() {
        $this->pdo = $this->connect();
    }

    /** @return Landmark[] */
    public function getAll(): array
    {
        $sql = "SELECT l.*,
                ec.event_id as event_category_id,
                ec.title as event_category_title,
                ec.type as event_category_type,
                ec.slug as event_category_slug
            FROM LANDMARK l
            LEFT JOIN EVENT_CATEGORIES ec ON l.event_id = ec.event_id
            ORDER BY l.display_order ASC";

        try {
            $stmt = $this->pdo->query($sql);
            return array_map(function (array $row): Landmark {
                $lm = new Landmark();
                $lm->fromPDOData($row);
                return $lm;
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch all landmarks: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getFeatured(): array
    {
        $sql = "SELECT l.*,
                    ec.event_id   as event_category_id,
                    ec.title      as event_category_title,
                    ec.type       as event_category_type,
                    ec.slug       as event_category_slug,
                    m.media_id    as main_image_media_id,
                    m.file_path   as main_image_file_path,
                    m.alt_text    as main_image_alt_text
                FROM LANDMARK l
                LEFT JOIN EVENT_CATEGORIES ec ON l.event_id = ec.event_id
                LEFT JOIN MEDIA m ON l.main_image_id = m.media_id
                WHERE l.is_featured = 1
                ORDER BY l.display_order ASC";

        try {
            $stmt = $this->pdo->query($sql);
            return array_map(function (array $row): Landmark {
                $lm = new Landmark();
                $lm->fromPDOData($row);
                return $lm;
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch featured landmarks: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getBySlug(string $slug): ?Landmark
    {
        $sql = "SELECT landmark.*,
                       media.media_id, media.file_path, media.alt_text,
                       gm.display_order,
                       main_img.media_id  AS main_image_media_id,
                       main_img.file_path AS main_image_file_path,
                       main_img.alt_text  AS main_image_alt_text
                FROM LANDMARK landmark
                LEFT JOIN GALLERY_MEDIA gm ON landmark.gallery_id = gm.gallery_id
                LEFT JOIN MEDIA media ON gm.media_id = media.media_id
                LEFT JOIN MEDIA main_img ON landmark.main_image_id = main_img.media_id
                WHERE landmark.landmark_slug = :slug
                ORDER BY gm.display_order ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) return null;
            return $this->hydrateOneLandmark($rows);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch landmark by slug: ' . $e->getMessage(), 0, $e);
        }
    }

    /** @return Landmark[] */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT landmark.*,
                       media.media_id, media.file_path, media.alt_text,
                       gm.display_order,
                       main_img.media_id  AS main_image_media_id,
                       main_img.file_path AS main_image_file_path,
                       main_img.alt_text  AS main_image_alt_text
                FROM LANDMARK landmark
                LEFT JOIN GALLERY_MEDIA gm ON landmark.gallery_id = gm.gallery_id
                LEFT JOIN MEDIA media ON gm.media_id = media.media_id
                LEFT JOIN MEDIA main_img ON landmark.main_image_id = main_img.media_id
                ORDER BY landmark.display_order ASC, gm.display_order ASC";

        try {
            $stmt  = $this->pdo->query($sql);
            $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $landmarks = [];
            foreach ($rows as $row) {
                $id = (int)$row['landmark_id'];

                if (!isset($landmarks[$id])) {
                    $lm = new Landmark();
                    $lm->fromPDOData($row);

                    if (!empty($row['gallery_id'])) {
                        $gallery = new \App\Models\Gallery();
                        $gallery->gallery_id = $row['gallery_id'];
                        $lm->gallery = $gallery;
                    }

                    $landmarks[$id] = $lm;
                }

                if (!empty($row['media_id']) && isset($landmarks[$id]->gallery)) {
                    $media = new \App\Models\Media();
                    $media->fromPDOData([
                        'media_id'  => $row['media_id'],
                        'file_path' => $row['file_path'],
                        'alt_text'  => $row['alt_text'] ?? ''
                    ]);

                    $galleryMedia = new \App\Models\GalleryMedia();
                    $galleryMedia->media = $media;
                    $galleryMedia->order = $row['display_order'] ?? 0;

                    $landmarks[$id]->gallery->addGalleryMedia($galleryMedia);
                }
            }

            return array_values($landmarks);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch all landmarks with details: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getById(int $id): ?Landmark
    {
        $sql = "SELECT landmark.*,
                       media.media_id, media.file_path, media.alt_text,
                       gm.display_order,
                       main_img.media_id  AS main_image_media_id,
                       main_img.file_path AS main_image_file_path,
                       main_img.alt_text  AS main_image_alt_text
                FROM LANDMARK landmark
                LEFT JOIN GALLERY_MEDIA gm ON landmark.gallery_id = gm.gallery_id
                LEFT JOIN MEDIA media ON gm.media_id = media.media_id
                LEFT JOIN MEDIA main_img ON landmark.main_image_id = main_img.media_id
                WHERE landmark.landmark_id = :id
                ORDER BY gm.display_order ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) return null;
            return $this->hydrateOneLandmark($rows);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch landmark by id: ' . $e->getMessage(), 0, $e);
        }
    }

    public function insert(Landmark $landmark): Landmark
    {
        $sql = "INSERT INTO LANDMARK (
            event_id, name, short_description, landmark_slug,
            intro_title, intro_content, why_visit_title, why_visit_content,
            detail_history_title, detail_history_content, display_order,
            latitude, longitude, is_featured, home_cta
        ) VALUES (
            :event_id, :name, :short_description, :landmark_slug,
            :intro_title, :intro_content, :why_visit_title, :why_visit_content,
            :detail_history_title, :detail_history_content, :display_order,
            :latitude, :longitude, :is_featured, :home_cta
        )";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->toLandmarkParams($landmark));
            $landmark->landmark_id = (int)$this->pdo->lastInsertId();
            return $landmark;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to insert landmark: ' . $e->getMessage(), 0, $e);
        }
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
                    display_order = :display_order,
                    latitude = :latitude,
                    longitude = :longitude,
                    is_featured = :is_featured,
                    home_cta = :home_cta
                WHERE landmark_id = :landmark_id";

        try {
            $stmt   = $this->pdo->prepare($sql);
            $params = $this->toLandmarkParams($landmark);
            $params[':landmark_id'] = $landmark->landmark_id;
            $stmt->execute($params);
            return $landmark;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to update landmark: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM LANDMARK WHERE landmark_id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to delete landmark: ' . $e->getMessage(), 0, $e);
        }
    }

    public function updateMainImage(int $landmarkId, int $mediaId): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE LANDMARK SET main_image_id = :media_id WHERE landmark_id = :id");
            $stmt->execute([':media_id' => $mediaId, ':id' => $landmarkId]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to update landmark main image: ' . $e->getMessage(), 0, $e);
        }
    }

    private function toLandmarkParams(Landmark $landmark): array
    {
        return [
            ':event_id'               => $landmark->event_id,
            ':name'                   => $landmark->name,
            ':short_description'      => $landmark->short_description,
            ':landmark_slug'          => $landmark->landmark_slug,
            ':intro_title'            => $landmark->intro_title,
            ':intro_content'          => $landmark->intro_content,
            ':why_visit_title'        => $landmark->why_visit_title,
            ':why_visit_content'      => $landmark->why_visit_content,
            ':detail_history_title'   => $landmark->detail_history_title,
            ':detail_history_content' => $landmark->detail_history_content,
            ':display_order'          => $landmark->display_order,
            ':latitude'               => $landmark->latitude,
            ':longitude'              => $landmark->longitude,
            ':is_featured'            => $landmark->is_featured ? 1 : 0,
            ':home_cta'               => $landmark->home_cta,
        ];
    }

    private function hydrateOneLandmark(array $rows): Landmark
    {
        $landmark = new Landmark();
        $landmark->fromPDOData($rows[0]);

        if (!empty($rows[0]['gallery_id'])) {
            $gallery = new Gallery();
            $gallery->gallery_id = $rows[0]['gallery_id'];

            foreach ($rows as $row) {
                if (!empty($row['media_id'])) {
                    $media = new Media();
                    $media->fromPDOData([
                        'media_id'  => $row['media_id'],
                        'file_path' => $row['file_path'],
                        'alt_text'  => $row['alt_text'] ?? ''
                    ]);
                    $galleryMedia = new GalleryMedia();
                    $galleryMedia->media = $media;
                    $galleryMedia->order = $row['display_order'] ?? 0;
                    $gallery->addGalleryMedia($galleryMedia);
                }
            }
            $landmark->gallery = $gallery;
        }

        return $landmark;
    }

    public function createGalleryForLandmark(int $landmarkId, string $title = 'Landmark Gallery'): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO GALLERY (title) VALUES (:title)");
            $stmt->bindValue(':title', $title);
            $stmt->execute();
            $galleryId = (int)$this->pdo->lastInsertId();

            $stmt2 = $this->pdo->prepare("UPDATE LANDMARK SET gallery_id = :gallery_id WHERE landmark_id = :landmark_id");
            $stmt2->bindValue(':gallery_id', $galleryId, PDO::PARAM_INT);
            $stmt2->bindValue(':landmark_id', $landmarkId, PDO::PARAM_INT);
            $stmt2->execute();

            $this->pdo->commit();
            return $galleryId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

}