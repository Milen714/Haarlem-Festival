<?php

namespace App\Services\Interfaces;

use App\Models\Schedule;

interface IScheduleService
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
     * Passing null for both returns every schedule in the system.
     *
     * @param string|null $eventType  Filter by festival strand (e.g. 'Jazz', 'Yummy'). Null = no filter.
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
     * Returns all schedule slots for a specific artist within an event, grouped by date.
     * Each slot includes venue info, sold-out status, and lowest ticket price.
     *
     * @param int $artistId  The artist whose slots you want.
     * @param int $eventId   The event to search within.
     *
     * @return array<string, array[]>  Slots grouped by 'Y-m-d' date string, sorted by start_time.
     */
    public function getSchedulesForArtistInEvent(int $artistId, int $eventId): array;

    /**
     * Returns all schedule slots within an event where the artist is flagged as a special-event act.
     *
     * @param int $eventId  The event to search within.
     *
     * @return Schedule[]
     */
    public function getBackToBackSpecialsByEventId(int $eventId): array;

    /**
     * Returns a sorted list of distinct future schedule dates in 'Y-m-d' format.
     *
     * @return string[]
     */
    public function getAvailableDates(): array;

    /**
     * Creates a new schedule from form POST data. Validates before persisting.
     *
     * @param array $postData  The raw $_POST data from the schedule create form.
     *
     * @return Schedule  The newly created Schedule with its generated schedule_id set.
     */
    public function createFromRequest(array $postData): \App\Models\Schedule;

    /**
     * Updates an existing schedule from form POST data. Fetches, validates, applies, and saves.
     *
     * @param int   $scheduleId  The primary key of the schedule to update.
     * @param array $postData    The raw $_POST data from the schedule edit form.
     *
     * @return Schedule  The updated Schedule after all changes have been persisted.
     */
    public function updateFromRequest(int $scheduleId, array $postData): \App\Models\Schedule;

    /**
     * Deletes a schedule. Throws if the schedule does not exist.
     *
     * @param int $scheduleId  The primary key of the schedule to delete.
     *
     * @return bool  True if deletion succeeded.
     */
    public function deleteSchedule(int $scheduleId): bool;

    /**
     * Returns all schedule slots linked to a specific restaurant, with ticket types attached.
     *
     * @param int $restaurantId  The restaurant whose slots you want.
     *
     * @return Schedule[]
     */
    public function getSchedulesByRestaurant(int $restaurantId): array;

    /**
     * Returns all event categories as plain associative arrays for CMS form dropdowns.
     *
     * @return array[]  Each element has keys: event_id, type, title.
     */
    public function getAllEventCategories(): array;

    /**
     * Returns all venues sorted A–Z, used to populate the venue dropdown in the schedule form.
     *
     * @return \App\Models\Venue[]
     */
    public function getAllVenues(): array;

    /**
     * Returns all non-deleted artists sorted A–Z, used to populate the artist dropdown in the schedule form.
     *
     * @return \App\Models\MusicEvent\Artist[]
     */
    public function getAllArtists(): array;

    /**
     * Returns all restaurants for the schedule form dropdown.
     * Returns an empty array if RestaurantService is unavailable.
     *
     * @return \App\Models\Restaurant[]
     */
    public function getAllRestaurants(): array;

    /**
     * Returns all landmarks for the schedule form dropdown.
     *
     * @return \App\Models\Landmark[]
     */
    public function getAllLandmarks(): array;
}
