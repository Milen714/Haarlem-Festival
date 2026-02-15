<?php

namespace App\Services;

use App\Services\Interfaces\IVenueService;
use App\Repositories\Interfaces\IVenueRepository;

class VenueService implements IVenueService
{
    private IVenueRepository $venueRepository;

    public function __construct(IVenueRepository $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    /**
     * Get venues used by a specific event
     */
    public function getVenuesByEventId(int $eventId): array
    {
        return $this->venueRepository->getVenuesByEventId($eventId);
    }

    /**
     * Get venue by ID
     */
    public function getVenueById(int $venueId): ?object
    {
        return $this->venueRepository->getVenueById($venueId);
    }
}