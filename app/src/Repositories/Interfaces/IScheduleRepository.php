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
     * Get all schedules with optional filters
     * @param string|null $eventType Filter by event type (e.g., 'Yummy', 'Jazz', 'Magic')
     * @param string|null $date Filter by date (format: 'Y-m-d')
     * @return Schedule[]
     */
    public function getAllSchedules(?string $eventType = null, ?string $date = null): array;

    /**
     * Get schedules for a specific event
     * @return Schedule[]
     */
    public function getScheduleByEventId(int $eventId): array;
    /**
     * Get available dates for scheduling
     * @return string[] Array of dates in 'Y-m-d' format
     */

    /**
     * @param int $eventId
     * @return Schedule[]
     */
    public function getBackToBackSpecialsByEventId(int $eventId): array;

    public function getAvailableDates(): array;

    /**
     * Create a new schedule record
     */
    public function create(Schedule $schedule): bool;

    /**
     * Update an existing schedule record
     */
    public function update(Schedule $schedule): bool;

    /**
     * Delete a schedule by ID
     */
    public function delete(int $scheduleId): bool;

    /**
     * Get all event categories for dropdowns
     */
    public function getAllEventCategories(): array;
}