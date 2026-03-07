<?php

namespace App\Repositories;

use App\Models\Gallery;
use PDO;

class GalleryRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Inserta la relación en la tabla intermedia
    public function addMediaToGallery(int $galleryId, int $mediaId, int $order = 0): bool {
        $sql = "INSERT INTO GALLERY_MEDIA (gallery_id, media_id, display_order) 
                VALUES (:gallery_id, :media_id, :order)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'gallery_id' => $galleryId,
            'media_id'   => $mediaId,
            'order'      => $order
        ]);
    }
}