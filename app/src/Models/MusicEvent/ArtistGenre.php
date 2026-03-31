<?php
namespace App\Models\MusicEvent;

class ArtistGenre
{
    public ?int $artist_id = null;
    public ?int $genre_id = null;
    public bool $is_primary = false;

    public ?Genre $genre = null;

    /** Empty constructor; populate via fromPDOData() after fetching from the ARTIST_GENRE table. */
    public function __construct() {}

    /**
     * Hydrates this link record from a raw database row.
     * Maps the artist–genre association columns and the is_primary flag,
     * which indicates whether this genre is the artist's main/primary genre.
     *
     * @param array $data  A single ARTIST_GENRE row from PDO::FETCH_ASSOC.
     *
     * @return void
     */
    public function fromPDOData(array $data): void
    {
        $this->artist_id  = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
        $this->genre_id   = isset($data['genre_id'])  ? (int)$data['genre_id']  : null;
        $this->is_primary = !empty($data['is_primary']);
    }
}
