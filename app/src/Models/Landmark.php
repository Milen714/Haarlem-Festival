<?php
namespace App\Models;

use App\Models\Media;
use App\Models\Gallery;

class Landmark
{
    public ?int $landmark_id = null;
    public ?int $event_id = null;
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
    public ?Gallery $gallery = null;
    public ?int $display_order = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->landmark_id = isset($data['landmark_id']) ? (int)$data['landmark_id'] : null;
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        $this->name = $data['name'] ?? $data['landmark_name'] ?? null;
        $this->short_description = $data['short_description'] ?? null;
        $this->landmark_slug = $data['landmark_slug'] ?? null;
        $this->intro_title = $data['intro_title'] ?? null;
        $this->intro_content = $data['intro_content'] ?? null;
        $this->why_visit_title = $data['why_visit_title'] ?? null;
        $this->why_visit_content = $data['why_visit_content'] ?? null;
        $this->detail_history_title = $data['detail_history_title'] ?? null;
        $this->detail_history_content = $data['detail_history_content'] ?? null;
        $this->display_order = isset($data['display_order']) ? (int)$data['display_order'] : null;

    }
}