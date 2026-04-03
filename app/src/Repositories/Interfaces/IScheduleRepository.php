<?php

namespace App\Repositories\Interfaces;

use App\Models\Schedule;

interface IScheduleRepository
{
    /**
     * Fetches a single fully hydrated Schedule by its primary key.
     * Returns null if no matching row exists.
     *
     * @param int $scheduleId  The primary key of the schedule to retrieve.
     *
     * @return Schedule|null
     */
    public function getScheduleById(int $scheduleId): ?Schedule;

    /**
     * Returns all schedules with optional filtering by event type and/or date.
     * Passing null for both parameters returns every schedule in the system.
     *
     * @param string|null $eventType  Filter by festival strand type (e.g. 'Jazz', 'Yummy'). Null = no filter.
     * @param string|null $date       Filter by date in 'Y-m-d' format. Null = no filter.
     *
     * @return Schedule[]
     */
    public function getAllSchedules(?string $eventType = null, ?string $date = null): array;

    /**
     * Returns all schedule slots for a given event ordered by date then start time.
     *
     * @param int $eventId  The event whose complete schedule you want.
     *
     * @return Schedule[]
     */
    public function getSchedulesByEventId(int $eventId): array;

    /**
     * Returns all schedule slots linked to a specific restaurant, with ticket types attached.
     *
     * @param int $restaurantId  The restaurant whose slots you want.
     *
     * @return Schedule[]
     */
    public function getSchedulesByRestaurant(int $restaurantId): array;

    /**
     * Returns all schedule slots within an event where the linked artist is a special-event act.
     * Used to build the back-to-back specials section on Jazz event pages.
     *
     * @param int $eventId  The event to search within.
     *
     * @return Schedule[]
     */
    public function getBackToBackSpecialsByEventId(int $eventId): array;

    /**
     * Returns a sorted list of distinct future schedule dates (today or later) in 'Y-m-d' format.
     * Used to populate date-filter dropdowns.
     *
     * @return string[]
     */
    public function getAvailableDates(): array;

    /**
     * Inserts a new schedule row and sets schedule_id on the object to the generated key.
     *
     * @param Schedule $schedule  The schedule to persist.
     *
     * @return bool  True on success.
     */
    public function create(Schedule $schedule): bool;

    /**
     * Updates every editable field on an existing schedule identified by schedule_id.
     *
     * @param Schedule $schedule  The schedule with updated values and a valid schedule_id.
     *
     * @return bool  True on success.
     */
    public function update(Schedule $schedule): bool;

    /**
     * Permanently hard-deletes a schedule row by its primary key.
     *
     * @param int $scheduleId  The primary key of the schedule to remove.
     *
     * @return bool  True on success.
     */
    public function delete(int $scheduleId): bool;

    /**
     * Returns all event categories as plain associative arrays for use in CMS dropdowns.
     * Each element has keys: event_id, type, title.
     *
     * @return array[]
     */
    public function getAllEventCategories(): array;
}
