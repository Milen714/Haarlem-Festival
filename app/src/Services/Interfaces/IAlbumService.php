<?php
namespace App\Services\Interfaces;

use App\Models\MusicEvent\Album;

interface IAlbumService
{
    public function getAlbumsByArtistId(int $artistId): array;
    public function getAlbumById(int $albumId): ?Album;
    public function createFromRequest(array $postData, array $files): Album;
    public function updateFromRequest(int $albumId, array $postData, array $files): Album;
    public function deleteAlbum(int $albumId): bool;
}