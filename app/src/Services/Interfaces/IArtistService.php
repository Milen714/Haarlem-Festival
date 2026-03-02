<?php

namespace App\Services\Interfaces;

use App\Models\MusicEvent\Artist;

interface IArtistService
{
    public function getArtistsByEventId(int $eventId): array;
    
    public function getArtistBySlug(string $slug): ?Artist;
    
    public function getArtistById(int $artistId): ?Artist;

     public function getAllArtists(): array;

    public function createFromRequest(array $postData, array $files): Artist;
    
    public function updateFromRequest(int $artistId, array $postData, array $files): Artist;

    public function deleteArtist(int $artistId): bool;

    public function isArtistInEvent(int $artistId, int $eventId): bool;
}