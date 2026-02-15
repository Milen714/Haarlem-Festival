<?php

namespace App\Services\Interfaces;

interface IScheduleService
{
    public function getScheduleByEventId(int $eventId): array;
}