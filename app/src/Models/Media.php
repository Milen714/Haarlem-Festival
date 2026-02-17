<?php

namespace App\Models;

class Media
{
    public ?int $media_id = null;
    public ?string $file_path = 'Assets/Home/ImagePlaceholder.png';
    public ?string $alt_text = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->media_id = isset($data['media_id']) ? (int)$data['media_id'] : null;
        $this->file_path = $data['file_path'] ?? null;
        $this->alt_text = $data['alt_text'] ?? null;
    }

    public function fromPostData(array $data): void
    {
        $mediaId = $data['media_id'] ?? null;
        $this->media_id = ($mediaId === null || $mediaId === '') ? null : (int)$mediaId;
        $this->file_path = $data['file_path'] ?? '';
        $this->alt_text = $data['alt_text'] ?? '';
    }
}