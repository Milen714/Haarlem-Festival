<?php
namespace App\Models;
use DateTime;
use App\Models\GalleryMediaMedia; 
class Gallery { 
    public ?int $gallery_id = null; 
    public ?string $title = null; 
    public ?DateTime $created_at = null;
    /** @var GalleryMedia[] */ 
    public array $media_items = []; 

    public function __construct() { } 
    public function addGalleryMedia(GalleryMedia $galleryMedia): void { 
        $this->media_items[] = $galleryMedia; 
    }
    public function fromPostData(array $data): void 
    { 
        $this->gallery_id = isset($data['gallery_id']) ? (int)$data['gallery_id'] : null; 
        $this->title = $data['title'] ?? null; 
    }
    public function fromPDOData(array $data): void 
    { 
        $this->gallery_id = isset($data['gallery_id']) ? (int)$data['gallery_id'] : null; 
        $this->title = $data['gallery_title'] ?? null; 
        $this->created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : null; 
    }
}