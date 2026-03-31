<?php
namespace App\Models\MusicEvent;

class EventArtist
{
    public ?int $event_id = null;
    public ?int $artist_id = null;
    public bool $is_headliner = false;
    public ?int $performance_order = null;

    public ?Artist $artist = null;

    /** Empty constructor; populate via fromPDOData() after querying the EVENT_ARTIST join table. */
    public function __construct() {}

    /**
     * Maps a raw EVENT_ARTIST row onto this object.
     * Captures which event the artist is booked for, whether they are headlining,
     * and their billing position within the lineup (lower number = earlier in the order).
     *
     * @param array $data  A single EVENT_ARTIST row from PDO::FETCH_ASSOC.
     *
     * @return void
     */
    public function fromPDOData(array $data): void
    {
        $this->event_id          = isset($data['event_id'])          ? (int)$data['event_id']          : null;
        $this->artist_id         = isset($data['artist_id'])         ? (int)$data['artist_id']         : null;
        $this->is_headliner      = !empty($data['is_headliner']);
        $this->performance_order = isset($data['performance_order']) ? (int)$data['performance_order'] : null;
    }
}
