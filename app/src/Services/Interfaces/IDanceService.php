<?php

namespace App\Services\Interfaces;
interface IDanceService
{
    public function getDanceOverviewData(string $slug, int $eventId): array;
}