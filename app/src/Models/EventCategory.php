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
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        $this->type = EventType::from($data['type']);
        $this->title = $data['title'] ?? '';
        $this->category_description = $data['category_description'] ?? null;
        $this->slug = $data['slug'] ?? null;
    }
}