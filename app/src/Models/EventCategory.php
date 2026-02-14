<?php 
namespace App\Models;
use App\Models\Enums\EventType;

class EventCategory
{
    public ?int $event_id = null;
    public EventType $type;
    public string $title;
    public ?string $category_description = null;
    public ?string $slug = null;
    public function __construct(){}
    public function fromPDOData(array $data): void {
        $this->event_id = isset($data['event_category_id']) ? (int)$data['event_category_id'] : null;
        $this->type = EventType::from($data['event_category_type']);
        $this->title = $data['event_category_title'] ?? '';
        $this->category_description = $data['event_category_description'] ?? null;
        $this->slug = $data['event_category_slug'] ?? null;
    }

    public function fromPostData(array $data): void {
        $this->event_id = isset($data['event_category_id']) ? (int)$data['event_category_id'] : null;
        $this->type = EventType::from($data['event_category_type']);
        $this->title = $data['title'] ?? '';
        $this->category_description = $data['category_description'] ?? null;
        $this->slug = $data['slug'] ?? null;
    }
}