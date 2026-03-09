<?php
namespace App\Repositories\Interfaces;

interface IAlbumRepository
{
    public function getAlbumsByArtistId(int $artistId): array;
    
    // public function getAlbumBySlug(string $slug);
    
    // public function getAlbumById(int $albumId);

    // public function createAlbum(array $albumData);
    
    // public function updateAlbum(int $albumId, array $albumData);

    // public function deleteAlbum(int $albumId): bool;
}