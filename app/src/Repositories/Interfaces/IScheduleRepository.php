<?php

namespace App\Repositories\Interfaces;

interface IScheduleRepository
{
    public function getScheduleByEventId(int $eventId): array;
}