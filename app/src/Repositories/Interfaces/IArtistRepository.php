<?php

namespace App\Repositories\Interfaces;

use App\Models\MusicEvent\Artist;

interface IArtistRepository
{
    /**
     * Returns all artists linked to the given event, ordered headliners first then by performance order.
     *
     * @param int $eventId  The event to fetch the artist lineup for.
     *
     * @return Artist[]
     */
    public function getArtistsByEventId(int $eventId): array;

    /**
     * Fetches a single artist by URL slug, including profile image, genres, gallery, and albums.
     * Returns null if the artist does not exist or has been soft-deleted.
     *
     * @param string $slug  The URL slug, e.g. 'chet-baker'.
     *
     * @return Artist|null
     */
    public function getArtistBySlug(string $slug): ?Artist;

    /**
     * Fetches a single artist by primary key, including the profile image.
     * Returns null if not found or soft-deleted.
     *
     * @param int $artistId  The artist's primary key.
     *
     * @return Artist|null
     */
    public function getArtistById(int $artistId): ?Artist;

    /**
     * Returns all non-deleted artists sorted A–Z, including genres and event count.
     *
     * @return Artist[]
     */
    public function getAllArtists(): array;

    /**
     * Inserts a new artist row and sets artist_id on the object to the generated key.
     *
     * @param Artist $artist  The artist to persist.
     *
     * @return bool  True on success.
     */
    public function create(Artist $artist): bool;

    /**
     * Updates every editable field on an existing artist identified by artist_id.
     *
     * @param Artist $artist  The artist with updated values and a valid artist_id.
     *
     * @return bool  True on success.
     */
    public function update(Artist $artist): bool;

    /**
     * Soft-deletes an artist by setting deleted_at to NOW(). The row is kept but hidden from listings.
     *
     * @param int $artistId  The primary key of the artist to soft-delete.
     *
     * @return bool  True on success.
     */
    public function delete(int $artistId): bool;

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
     * Creates a new GALLERY row and links it to the given artist in a single transaction.
     *
     * @param int    $artistId  The artist who should own the new gallery.
     * @param string $title     A human-readable gallery title.
     *
     * @return int  The newly created gallery_id.
     */
    public function createGalleryForArtist(int $artistId, string $title): int;

    /**
     * Links a media file to a gallery at the specified display position.
     *
     * @param int $galleryId     The target gallery.
     * @param int $mediaId       The media record to add.
     * @param int $displayOrder  Sort position (lower = shown first).
     *
     * @return bool  True on success.
     */
    public function addMediaToGallery(int $galleryId, int $mediaId, int $displayOrder): bool;

    /**
     * Removes the link between a media file and a gallery without deleting the MEDIA record.
     *
     * @param int $galleryId  The gallery to remove the image from.
     * @param int $mediaId    The media record to unlink.
     *
     * @return bool  True on success.
     */
    public function removeMediaFromGallery(int $galleryId, int $mediaId): bool;

    /**
     * Returns the next available display_order for a gallery (MAX + 1, or 1 if empty).
     *
     * @param int $galleryId  The gallery to inspect.
     *
     * @return int  The next display_order value.
     */
    public function getNextGalleryOrder(int $galleryId): int;
}
