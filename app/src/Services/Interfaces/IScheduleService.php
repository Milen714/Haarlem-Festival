<?php

namespace App\Services\Interfaces;

use App\Models\Schedule;

interface IScheduleService
{
    /**
     * Get a single schedule by ID
     * @return Schedule|null
     */
    public function getScheduleById(int $scheduleId): ?Schedule;

    /**
     * Get all schedules as an array of Schedule objects
     * @return Schedule[]
     */
    public function getAllSchedules(): array;

    /**
     * Get schedules for a specific event as an array of Schedule objects
     * @return Schedule[]
     */
    public function getSchedulesByEventId(int $eventId): array;

    /**
     * Get schedule grouped by date for a specific event
     * @return array
     */
    public function getScheduleByEventIdGrouped(int $eventId): array;

    /**
     * Legacy method for backwards compatibility
     * @deprecated Use getScheduleByEventIdGrouped() instead
     * @return array
     */
    public function getScheduleByEventId(int $eventId): array;
}