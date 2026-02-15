<?php

namespace App\Repositories\Interfaces;

interface IArtistRepository
{
    public function getArtistsByEventId(int $eventId): array;
    public function getArtistBySlug(string $slug): ?object;
    public function getArtistById(int $artistId): ?object;
}