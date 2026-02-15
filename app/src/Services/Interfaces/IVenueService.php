<?php

namespace App\Services\Interfaces;

interface IVenueService
{
    public function getVenuesByEventId(int $eventId): array;
    public function getVenueById(int $venueId): ?object;
}