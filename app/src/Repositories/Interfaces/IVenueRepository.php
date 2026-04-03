<?php

namespace App\Repositories\Interfaces;

use App\Models\Venue;

interface IVenueRepository
{
    /**
     * Returns all distinct venues that have at least one schedule entry for the given event.
     *
     * @param int $eventId  The event to filter venues by (matched via SCHEDULE.event_id).
     *
     * @return Venue[]  Sorted A–Z by name.
     */
    public function getVenuesByEventId(int $eventId): array;

    /**
     * Fetches a single venue by its primary key, including image and EventCategory.
     * Returns null if no venue with that ID exists.
     *
     * @param int $venueId  The venue's primary key.
     *
     * @return Venue|null
     */
    public function getVenueById(int $venueId): ?Venue;

    /**
     * Returns all venues sorted A–Z, including image, EventCategory, and schedule count.
     *
     * @return Venue[]
     */
    public function getAllVenues(): array;

    /**
     * Inserts a new venue and sets venue_id on the object to the generated key.
     *
     * @param Venue $venue  The venue to persist.
     *
     * @return bool  True on success.
     */
    public function create(Venue $venue): bool;

    /**
     * Updates every editable field on an existing venue identified by venue_id.
     *
     * @param Venue $venue  The venue with updated values and a valid venue_id.
     *
     * @return bool  True on success.
     */
    public function update(Venue $venue): bool;

    /**
     * Permanently hard-deletes a venue row. The VENUE table has no soft-delete column.
     *
     * @param int $venueId  The primary key of the venue to remove.
     *
     * @return bool  True on success.
     */
    public function delete(int $venueId): bool;
}
