<?php

namespace App\Services\Interfaces;

use App\Models\Venue;

interface IVenueService
{
    /**
     * Returns all distinct venues that have at least one schedule entry for the given event.
     *
     * @param int $eventId  The event to filter venues by.
     *
     * @return Venue[]  Sorted A–Z by name.
     */
    public function getVenuesByEventId(int $eventId): array;

    /**
     * Fetches a single venue by its primary key. Returns null if not found.
     *
     * @param int $venueId  The venue's primary key.
     *
     * @return Venue|null
     */
    public function getVenueById(int $venueId): ?Venue;

    /**
     * Returns all venues sorted A–Z with image, EventCategory, and schedule count.
     *
     * @return Venue[]
     */
    public function getAllVenues(): array;

    /**
     * Creates a new venue from form POST data, handling the image upload.
     * Validates before persisting; throws on validation or upload failure.
     *
     * @param array $postData  The raw $_POST data from the create form.
     * @param array $files     The raw $_FILES array — expects a 'venue_image' key.
     *
     * @return Venue  The newly created and persisted Venue.
     */
    public function createFromRequest(array $postData, array $files): Venue;

    /**
     * Updates an existing venue from form POST data, handling image replacement.
     * Fetches the current record, validates, applies changes, then saves.
     *
     * @param int   $venueId   The primary key of the venue to update.
     * @param array $postData  The raw $_POST data from the edit form.
     * @param array $files     The raw $_FILES array — expects a 'venue_image' key if replacing.
     *
     * @return Venue  The updated Venue after all changes have been persisted.
     */
    public function updateFromRequest(int $venueId, array $postData, array $files): Venue;

    /**
     * Deletes a venue. Throws if the venue does not exist.
     *
     * @param int $venueId  The primary key of the venue to delete.
     *
     * @return bool  True if deletion succeeded.
     */
    public function deleteVenue(int $venueId): bool;
}
