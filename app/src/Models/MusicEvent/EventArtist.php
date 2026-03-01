<?php
namespace App\Models\MusicEvent;

class EventArtist
{
    public ?int $event_id = null;
    public ?int $artist_id = null;
    public bool $is_headliner = false;
    public ?int $performance_order = null;

    public ?Artist $artist = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->event_id          = isset($data['event_id'])          ? (int)$data['event_id']          : null;
        $this->artist_id         = isset($data['artist_id'])         ? (int)$data['artist_id']         : null;
        $this->is_headliner      = !empty($data['is_headliner']);
        $this->performance_order = isset($data['performance_order']) ? (int)$data['performance_order'] : null;
    }
}