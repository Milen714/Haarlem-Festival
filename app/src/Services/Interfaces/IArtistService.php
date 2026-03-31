<?php

namespace App\Services\Interfaces;

use App\Models\MusicEvent\Artist;

interface IArtistService
{
    /**
     * Returns all artists linked to the given event, ordered headliners first then by performance order.
     *
     * @param int $eventId  The event whose artist lineup you want.
     *
     * @return Artist[]
     */
    public function getArtistsByEventId(int $eventId): array;

    /**
     * Fetches a single artist by URL slug, including profile image, genres, gallery, and albums.
     * Returns null if not found or soft-deleted.
     *
     * @param string $slug  The URL slug, e.g. 'miles-davis'.
     *
     * @return Artist|null
     */
    public function getArtistBySlug(string $slug): ?Artist;

    /**
     * Fetches a single artist by primary key without gallery or albums.
     * Returns null if not found or soft-deleted.
     *
     * @param int $artistId  The artist's primary key.
     *
     * @return Artist|null
     */
    public function getArtistById(int $artistId): ?Artist;

    /**
     * Returns all non-deleted artists sorted A–Z with genres and event count.
     *
     * @return Artist[]
     */
    public function getAllArtists(): array;

    /**
     * Creates a new artist from form POST data, handling profile image upload.
     * Validates before persisting; throws on validation or upload failure.
     *
     * @param array $postData  The raw $_POST data from the create form.
     * @param array $files     The raw $_FILES array — expects a 'profile_image' key.
     *
     * @return Artist  The newly created and persisted Artist.
     */
    public function createFromRequest(array $postData, array $files): Artist;

    /**
     * Updates an existing artist from form POST data, handling profile image replacement.
     * Fetches the current record, validates, applies changes, then saves.
     *
     * @param int   $artistId  The primary key of the artist to update.
     * @param array $postData  The raw $_POST data from the edit form.
     * @param array $files     The raw $_FILES array — expects a 'profile_image' key if replacing.
     *
     * @return Artist  The updated Artist after all changes have been persisted.
     */
    public function updateFromRequest(int $artistId, array $postData, array $files): Artist;

    /**
     * Update artist and handle all gallery operations in one call.
     * Updates core artist fields and image, then processes gallery replacements and new uploads.
     * Consolidates complex business logic in the service layer so the controller stays thin.
     *
     * @param int   $artistId  The primary key of the artist to update.
     * @param array $postData  The raw $_POST data from the edit form.
     * @param array $files     The raw $_FILES array, which may include profile_image,
     *                         gallery_images (multi-file), and gallery_replace_{mediaId} keys.
     *
     * @return Artist  The updated Artist (core fields only; gallery is updated as a side effect).
     */
    public function updateArtistWithGalleryFromRequest(int $artistId, array $postData, array $files): Artist;

    /**
     * Soft-deletes an artist. Throws if the artist does not exist.
     *
     * @param int $artistId  The primary key of the artist to soft-delete.
     *
     * @return bool  True if deletion succeeded.
     */
    public function deleteArtist(int $artistId): bool;

    /**
     * Returns true if the artist is linked to the event via EVENT_ARTIST or SCHEDULE.
     *
     * @param int $artistId  The artist to check.
     * @param int $eventId   The event to check against.
     *
     * @return bool
     */
    public function isArtistInEvent(int $artistId, int $eventId): bool;

    /**
     * Fetches an artist by primary key together with their full gallery and ordered media items.
     * Returns null if not found or soft-deleted.
     *
     * @param int $artistId  The artist's primary key.
     *
     * @return Artist|null
     */
    public function getArtistByIdWithGallery(int $artistId): ?Artist;

    /**
     * Uploads one or more gallery images for an artist, creating a gallery first if needed.
     *
     * @param int         $artistId  The artist to add gallery images to.
     * @param Artist|null $artist    The current artist object used to resolve the gallery_id.
     * @param array       $files     The $_FILES['gallery_images'] array (multi-file upload shape).
     *
     * @return void
     */
    public function uploadGalleryImages(int $artistId, ?Artist $artist, array $files): void;

    /**
     * Removes the link between a media file and the artist's gallery (does not delete the MEDIA record).
     *
     * @param int $artistId  The artist who owns the gallery.
     * @param int $mediaId   The media record to unlink.
     *
     * @return bool  True if removed, false if artist or gallery not found.
     */
    public function removeGalleryImage(int $artistId, int $mediaId): bool;
}
