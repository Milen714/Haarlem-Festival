<?php
namespace App\Models\MusicEvent;

class ArtistGenre
{
    public ?int $artist_id = null;
    public ?int $genre_id = null;
    public bool $is_primary = false;

    public ?Genre $genre = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->artist_id  = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
        $this->genre_id   = isset($data['genre_id'])  ? (int)$data['genre_id']  : null;
        $this->is_primary = !empty($data['is_primary']);
    }
}