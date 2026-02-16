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
        $this->gallery_id = isset($data['gallery_id']) ? (int)$data['gallery_id'] : null;
        $this->order = isset($data['display_order']) ? (int)$data['display_order'] : null;
    }

}