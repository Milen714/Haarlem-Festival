<?php
namespace App\Services\Interfaces;

interface IAlbumService
{
    public function getAlbumsByArtistId(int $artistId): array;
    
    // public function getAlbumBySlug(string $slug);
    
    // public function getAlbumById(int $albumId);

    // public function createFromRequest(array $postData, array $files);
    
    // public function updateFromRequest(int $albumId, array $postData, array $files);

    // public function deleteAlbum(int $albumId): bool;
}