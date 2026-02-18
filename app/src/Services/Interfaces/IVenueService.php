<?php

namespace App\Services\Interfaces;

use App\Models\Venue;

interface IVenueService
{
    public function getVenuesByEventId(int $eventId): array;
    
    public function getVenueById(int $venueId): ?Venue;

    public function getAllVenues(): array;
  
    public function createFromRequest(array $postData, array $files): Venue;

    public function updateFromRequest(int $venueId, array $postData, array $files): Venue;

    public function deleteVenue(int $venueId): bool;
}