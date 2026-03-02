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
     * Get all schedules with optional filters
     * @param string|null $eventType Filter by event type (e.g., 'Yummy', 'Jazz', 'Magic')
     * @param string|null $date Filter by date (format: 'Y-m-d')
     * @return Schedule[]
     */
    public function getAllSchedules(?string $eventType = null, ?string $date = null): array;

    /**
     * Get schedules for a specific event as an array of Schedule objects
     * @return Schedule[]
     */
    public function getSchedulesByEventId(int $eventId): array;

     public function getSchedulesForArtistInEvent(int $artistId, int $eventId): array;

 
   
}