<?php

namespace App\Repositories\Interfaces;

use App\Models\MusicEvent\Artist;

interface IArtistRepository
{

    public function getArtistsByEventId(int $eventId): array;
   
    public function getArtistBySlug(string $slug): ?Artist;
    
    public function getArtistById(int $artistId): ?Artist;
     
    public function getAllArtists(): array;
  
    public function create(Artist $artist): bool;

    public function update(Artist $artist): bool;
 
    public function delete(int $artistId): bool;

    public function isArtistInEvent(int $artistId, int $eventId): bool;
}