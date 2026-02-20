<?php

namespace App\Services;

use App\Services\Interfaces\IArtistService;
use App\Repositories\Interfaces\IArtistRepository;

class ArtistService implements IArtistService
{
    private IArtistRepository $artistRepository;

    public function __construct(IArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    /**
     * Get all artists for a specific event
     */
    public function getArtistsByEventId(int $eventId): array
    {
        return $this->artistRepository->getArtistsByEventId($eventId);
    }

    /**
     * Get artist by slug
     */
    public function getArtistBySlug(string $slug): ?object
    {
        return $this->artistRepository->getArtistBySlug($slug);
    }

    /**
     * Get artist by ID
     */
    public function getArtistById(int $artistId): ?object
    {
        return $this->artistRepository->getArtistById($artistId);
    }
}