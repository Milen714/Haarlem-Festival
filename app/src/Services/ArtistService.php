<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\ArtistRepository;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IMediaService;
use App\Repositories\Interfaces\IArtistRepository;
use App\Models\MusicEvent\Artist;

class ArtistService implements IArtistService
{
    private IArtistRepository $artistRepository;
    private IMediaService $mediaService;

    /**
     * Wires up the ArtistRepository for data access and MediaService for handling
     * profile image and gallery uploads.
     */
    public function __construct() {
        $this->artistRepository = new ArtistRepository();
        $this->mediaService = new MediaService();
    }

    public function getArtistsByEventId(int $eventId): array
    {
        return $this->artistRepository->getArtistsByEventId($eventId);
    }

    public function getArtistById(int $artistId): ?Artist
    {
        return $this->artistRepository->getArtistById($artistId);
    }

    public function getArtistBySlug(string $slug): ?Artist
    {
        return $this->artistRepository->getArtistBySlug($slug);
    }

    public function getAllArtists(): array
    {
        return $this->artistRepository->getAllArtists();
    }

    public function createFromRequest(array $postData, array $files): Artist
    {
        $this->validateArtistData($postData);

        $artist = Artist::createFromPostData($postData);

        $artist = $this->handleImageUpload($artist, $files);

        $this->artistRepository->create($artist);

        return $artist;
    }

    public function updateFromRequest(int $artistId, array $postData, array $files): Artist
    {

        $artist = $this->artistRepository->getArtistById($artistId);

        if (!$artist) {
            throw new ResourceNotFoundException('Artist not found.');
        }

        $this->validateArtistData($postData);

        $artist->applyPostData($postData);

        $artist = $this->handleImageUpload($artist, $files);

        $this->artistRepository->update($artist);

        return $artist;
    }

    /**
     * Update order: core fields → fresh gallery load → replace existing images → upload new images.
     * Returns the core Artist (without re-fetched gallery); the gallery is updated as a side effect.
     */
    public function updateArtistWithGalleryFromRequest(int $artistId, array $postData, array $files): Artist
    {
        // Update core artist data
        $artist = $this->updateFromRequest($artistId, $postData, $files);

        // Load fresh copy with gallery for subsequent operations
        $artistWithGallery = $this->artistRepository->getArtistByIdWithGallery($artistId);

        if (!$artistWithGallery) {
            throw new ResourceNotFoundException('Artist not found.');
        }

        // Handle gallery image replacements
        $this->replaceGalleryImagesFromRequest($artistWithGallery, $files);

        // Handle new gallery image uploads
        if ($this->hasUploads($files['gallery_images'] ?? null)) {
            $this->uploadGalleryImages($artistId, $artistWithGallery, $files['gallery_images']);
        }

        return $artist;
    }

    /**
     * Handles both single-file and multi-file upload array shapes.
     * Returns false when the input is null or has no populated name entries.
     */
    private function hasUploads(?array $fileInput): bool
    {
        if (!$fileInput || !isset($fileInput['name'])) {
            return false;
        }

        if (is_array($fileInput['name'])) {
            foreach ($fileInput['name'] as $name) {
                if (!empty($name)) {
                    return true;
                }
            }
            return false;
        }

        return !empty($fileInput['name']);
    }

    public function deleteArtist(int $artistId): bool
    {
        $artist = $this->artistRepository->getArtistById($artistId);

        if (!$artist) {
            throw new ResourceNotFoundException('Artist not found.');
        }

        return $this->artistRepository->delete($artistId);
    }

    /**
     * Validates name presence, length bounds (2–100 chars), and URL format for website/Spotify.
     */
    private function validateArtistData(array $data): void
    {
        if (empty($data['name'])) {
            throw new ValidationException('Artist name is required.');
        }

        if (strlen($data['name']) < 2) {
            throw new ValidationException('Artist name must be at least 2 characters.');
        }

        if (strlen($data['name']) > 100) {
            throw new ValidationException('Artist name cannot exceed 100 characters.');
        }

        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Invalid website URL.');
        }

        if (!empty($data['spotify_url']) && !filter_var($data['spotify_url'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Invalid Spotify URL.');
        }
    }

    /**
     * Skips silently when no file is present or the upload errored.
     * Calls replaceMedia() when the artist already has a media_id (update path),
     * otherwise uploadAndCreate() (create path). Throws ApplicationException on failure.
     */
    private function handleImageUpload(Artist $artist, array $files): Artist
    {
        if (!isset($files['profile_image']) || $files['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return $artist;
        }

        $isUpdate = $artist->profile_image && $artist->profile_image->media_id;

        if ($isUpdate) {
            $result = $this->mediaService->replaceMedia(
                $artist->profile_image->media_id,
                $files['profile_image'],
                'Artists',
                $artist->name . ' profile'
            );
        } else {
            $result = $this->mediaService->uploadAndCreate(
                $files['profile_image'],
                'Artists',
                $artist->name . ' profile'
            );
        }

        if ($result['success'] && isset($result['media'])) {
            $artist->profile_image = $result['media'];
        } else {
            throw new ApplicationException('Failed to upload artist image.');
        }

        return $artist;
    }

    public function isArtistInEvent(int $artistId, int $eventId): bool
    {
        return $this->artistRepository->isArtistInEvent($artistId, $eventId);
    }

    public function getArtistByIdWithGallery(int $artistId): ?Artist
    {
        return $this->artistRepository->getArtistByIdWithGallery($artistId);
    }

    /**
     * Creates a gallery row first if the artist has none yet, then uploads each file to
     * 'Jazz/Artists' and links it via addMediaToGallery(). Skips files with upload errors
     * rather than aborting the whole batch.
     */
    public function uploadGalleryImages(int $artistId, ?Artist $artist, array $files): void
    {
        // Normalise the multi-file upload array so every entry is a single-file array
        $uploads = $this->normaliseMultiFileArray($files);

        if (empty($uploads)) {
            return;
        }

        // Ensure the artist has a gallery row; create one if not
        $galleryId = $artist?->gallery?->gallery_id;
        if (!$galleryId) {
            $galleryId = $this->artistRepository->createGalleryForArtist(
                $artistId,
                ($artist?->name ?? 'Artist') . ' Gallery'
            );
        }

        foreach ($uploads as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $altText = $artist?->name ? $artist->name . ' gallery image' : 'Gallery image';
            $result  = $this->mediaService->uploadAndCreate($file, 'Jazz/Artists', $altText);

            if ($result['success'] && isset($result['media'])) {
                $order = $this->artistRepository->getNextGalleryOrder($galleryId);
                $this->artistRepository->addMediaToGallery($galleryId, $result['media']->media_id, $order);
            } else {
                error_log("Gallery image upload failed: " . ($result['error'] ?? 'unknown'));
            }
        }
    }

    /**
     * Scans $_FILES for keys matching 'gallery_replace_{mediaId}'. Validates each mediaId
     * against the artist's actual gallery to prevent replacing images that don't belong to them.
     */
    public function replaceGalleryImagesFromRequest(?Artist $artist, array $files): void
    {
        if (!$artist || !$artist->gallery || empty($artist->gallery->media_items) || empty($files)) {
            return;
        }

        $allowedMediaIds = [];
        foreach ($artist->gallery->media_items as $galleryMedia) {
            $mediaId = (int)($galleryMedia->media->media_id ?? 0);
            if ($mediaId > 0) {
                $allowedMediaIds[$mediaId] = true;
            }
        }

        foreach ($files as $key => $file) {
            if (!is_string($key) || !str_starts_with($key, 'gallery_replace_')) {
                continue;
            }

            $mediaId = (int)str_replace('gallery_replace_', '', $key);
            if ($mediaId <= 0 || !isset($allowedMediaIds[$mediaId])) {
                continue;
            }

            if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $altText = ($artist->name ?: 'Artist') . ' gallery image';
            $result = $this->mediaService->replaceMedia($mediaId, $file, 'Jazz/Artists', $altText);

            if (!($result['success'] ?? false)) {
                throw new ApplicationException('Failed to replace gallery image.');
            }
        }
    }

    public function removeGalleryImage(int $artistId, int $mediaId): bool
    {
        $artist = $this->artistRepository->getArtistByIdWithGallery($artistId);

        if (!$artist || !$artist->gallery?->gallery_id) {
            return false;
        }

        return $this->artistRepository->removeMediaFromGallery($artist->gallery->gallery_id, $mediaId);
    }

    /**
     * PHP's multi-file upload arrays use parallel sub-arrays (name[], type[], etc.).
     * This flattens them into a list of per-file arrays so they can be iterated uniformly.
     * Also handles the single-file case (name is a string) by wrapping it as-is.
     */
    private function normaliseMultiFileArray(array $files): array
    {
        if (!isset($files['name'])) {
            return [];
        }

        if (is_string($files['name'])) {
            return [$files];
        }

        $result = [];
        foreach ($files['name'] as $i => $name) {
            if (empty($name)) {
                continue;
            }

            // Ensure all required keys exist before accessing them
            if (!isset($files['type'][$i], $files['tmp_name'][$i], $files['error'][$i], $files['size'][$i])) {
                continue;
            }

            $result[] = [
                'name'     => $name,
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
        }
        return $result;
    }
}
