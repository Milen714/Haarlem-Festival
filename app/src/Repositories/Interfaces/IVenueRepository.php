<?php

namespace App\Repositories\Interfaces;

use App\Models\Venue;

interface IVenueRepository
{
    public function getVenuesByEventId(int $eventId): array;
    
    public function getVenueById(int $venueId): ?Venue;
     
    public function getAllVenues(): array;
    
  
    public function create(Venue $venue): bool;

    public function update(Venue $venue): bool;

    public function delete(int $venueId): bool;
}