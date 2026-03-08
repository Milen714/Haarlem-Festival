<?php

namespace App\Repositories;

use App\Models\Gallery;
use App\Framework\Repository; 
use PDO;

class GalleryRepository extends Repository 
{
    public function __construct() {
        $this->pdo = $this->connect(); 
    }

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