<?php

namespace App\Repositories\Interfaces;

use App\Models\Schedule;

interface IScheduleRepository
{
    /**
     * Get a single schedule by ID
     * @return Schedule|null
     */
    public function getScheduleById(int $scheduleId): ?Schedule;

    /**
     * Get all schedules
     * @return Schedule[]
     */
    public function getAllSchedules(): array;

    /**
     * Get schedules for a specific event
     * @return Schedule[]
     */
    public function getScheduleByEventId(int $eventId): array;
}