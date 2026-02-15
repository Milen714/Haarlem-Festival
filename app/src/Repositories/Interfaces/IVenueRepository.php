<?php

namespace App\Repositories\Interfaces;

interface IVenueRepository
{
    public function getVenuesByEventId(int $eventId): array;
    public function getVenueById(int $venueId): ?object;
}