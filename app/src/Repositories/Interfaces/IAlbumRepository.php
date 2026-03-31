<?php
namespace App\Repositories\Interfaces;

use App\Models\MusicEvent\Album;

interface IAlbumRepository
{
    public function getAlbumsByArtistId(int $artistId): array;
    public function getAlbumById(int $albumId): ?Album;
    public function create(Album $album): bool;
    public function update(Album $album): bool;
    public function delete(int $albumId): bool;
}