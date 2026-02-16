<?php
namespace App\Models\MusicEvent;

use App\Models\Media;

class Album
{
    public ?int $album_id = null;
    public ?int $artist_id = null;
    public ?string $name = null;
    public ?string $release_year = null;
    public ?string $description = null;
    public ?Media $cover_image = null;
    public ?string $spotify_url = null;
   

    public function __construct(){}

    public function fromPDOData(array $data): void {
        $this->album_id = isset($data['album_id']) ? (int)$data['album_id'] : null;
        $this->artist_id = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
        $this->name = $data['name'] ?? null;
        $this->release_year = $data['release_year'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->spotify_url = $data['spotify_url'] ?? null;
    }

        
    public function fromPostData(array $data): void {
        $albumId = $data['album_id'] ?? null;
        $this->album_id = ($albumId === null || $albumId === '') ? null : (int)$albumId;
        $artistId = $data['artist_id'] ?? null;
        $this->artist_id = ($artistId === null || $artistId === '') ? null : (int)$artistId;
        $this->name = $data['name'] ?? '';
        $this->release_year = $data['release_year'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->spotify_url = $data['spotify_url'] ?? '';
    }
}