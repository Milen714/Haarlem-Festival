<?php

namespace App\Services\Interfaces;

interface IArtistService
{
    public function getArtistsByEventId(int $eventId): array;
    public function getArtistBySlug(string $slug): ?object;
    public function getArtistById(int $artistId): ?object;
}