<?php
namespace App\Models;
use App\Models\Media;
class GalleryMedia
{
    
    public ?int $gallery_id;
    public ?Media $media = null;
    public ?int $order;
    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->gallery_id = $data['gallery_id'] ?? (int)$data['section_gallery_id'] ?? null;
        $this->order = $data['display_order'] ?? (int)$data['gallery_media_display_order'] ?? null;
    }

}