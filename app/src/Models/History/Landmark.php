<?php
namespace App\Models\History;

use App\Models\Media;
use App\Models\Gallery;
use App\Models\EventCategory;

class Landmark
{
    public ?int $landmark_id = null;
    public ?int $event_id = null;
    public ?string $name = null;
    //public ?string $landmark_title = null;
    public ?string $short_description = null;
    public ?Media $landmark_image = null;
    public ?bool $has_detail_page = null;
    public ?string $landmark_slug = null;
    public ?string $detail_intro_content = null;
    public ?string $detail_why_content = null;
    public ?string $detail_history_content = null;
    public ?Gallery $gallery = null;
    public ?int $display_order = null;
    public ?EventCategory $event_category = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->landmark_id = isset($data['landmark_id']) ? (int)$data['landmark_id'] : null;
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        $this->name = $data['name'] ?? $data['landmark_name'] ?? null;
        //$this->landmark_title = $data['landmark_title'] ?? null;
        $this->short_description = $data['short_description'] ?? $data['landmark_short_description'] ?? null;
        $this->has_detail_page = isset($data['has_detail_page']) ? (bool)$data['has_detail_page'] : false;
        $this->landmark_slug = $data['landmark_slug'] ?? null;
        $this->detail_intro_content = $data['detail_intro_content'] ?? null;
        $this->detail_why_content = $data['detail_why_content'] ?? null;
        $this->detail_history_content = $data['detail_history_content'] ?? null;
        $this->display_order = isset($data['display_order']) ? (int)$data['display_order'] : null;

        // Hydrate landmark image if available
        if (isset($data['landmark_image_id']) && $data['landmark_image_id'] !== null) {
            $this->landmark_image = new Media();
            $this->landmark_image->fromPDOData([
                'media_id' => $data['landmark_image_id'],
                'file_path' => $data['landmark_image_path'] ?? null,
                'alt_text' => $data['landmark_image_alt'] ?? null,
            ]);
        }
        // Hydrate event category if available
        if (isset($data['event_category_id']) && $data['event_category_id'] !== null) {
            $this->event_category = new EventCategory();
            $this->event_category->fromPDOData([
            'event_category_id' => $data['event_category_id'],
            'event_category_title' => $data['event_category_title'] ?? null,
            'event_category_type' => $data['event_category_type'] ?? null,
            'event_category_slug' => $data['event_category_slug'] ?? null,
        ]);
        }
    }
}