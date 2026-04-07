<?php
namespace App\Models;

use App\Models\Media;
use App\Models\Gallery;
use App\Models\EventCategory;

class Landmark
{
    public ?int $landmark_id = null;
    public ?int $event_id = null;
    public ?EventCategory $event_category = null;
    public ?string $name = null;
    public ?string $short_description = null;
    public ?Media $main_image_id = null;
    public ?string $landmark_slug = null;
    public ?string $intro_title = null;
    public ?string $intro_content = null;
    public ?string $why_visit_title = null;
    public ?string $why_visit_content = null;
    public ?string $detail_history_title = null;
    public ?string $detail_history_content = null;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?Gallery $gallery = null;
    public ?int $display_order = null;
    public bool $is_featured = false;
    public ?string $home_cta = null;
    public ?string $imagePath = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        foreach (['landmark_id', 'event_id', 'display_order'] as $field) {
            $this->$field = isset($data[$field]) ? (int)$data[$field] : null;
        }
        foreach (['latitude', 'longitude'] as $field) {
            $this->$field = isset($data[$field]) ? (float)$data[$field] : null;
        }
        foreach (['short_description', 'landmark_slug', 'intro_title', 'intro_content',
                  'why_visit_title', 'why_visit_content', 'detail_history_title',
                  'detail_history_content', 'home_cta'] as $field) {
            $this->$field = $data[$field] ?? null;
        }

        $this->name       = $data['landmark_name'] ?? $data['name'] ?? null;
        $this->is_featured = !empty($data['is_featured']);

        if (isset($data['event_category_type'])) {
            $this->event_category = new EventCategory();
            $this->event_category->fromPDOData([
                'event_category_id'    => $data['event_category_id'] ?? null,
                'event_category_title' => $data['event_category_title'] ?? null,
                'event_category_type'  => $data['event_category_type'],
                'event_category_slug'  => $data['event_category_slug'] ?? null,
            ]);
        }

        if (isset($data['main_image_media_id'])) {
            $this->main_image_id = new Media();
            $this->main_image_id->fromPDOData([
                'media_id'  => $data['main_image_media_id'],
                'file_path' => $data['main_image_file_path'] ?? null,
                'alt_text'  => $data['main_image_alt_text'] ?? null,
            ]);
        }
    }
}