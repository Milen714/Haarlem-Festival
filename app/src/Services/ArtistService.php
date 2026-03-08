<?php

namespace App\Services;

use App\Services\Interfaces\IArtistService;
use App\Repositories\Interfaces\IArtistRepository;
use App\Models\MusicEvent\Artist;

class ArtistService implements IArtistService
{
    private IArtistRepository $artistRepository;
    private MediaService $mediaService;

    public function __construct(
        IArtistRepository $artistRepository,
        MediaService $mediaService
    ) {
        $this->artistRepository = $artistRepository;
        $this->mediaService = $mediaService;
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
       
        $artist = $this->buildArtistFromPostData($postData);
        
        $artist = $this->handleImageUpload($artist, $files);
        
        $this->artistRepository->create($artist);
        
        return $artist;
    }

    public function updateFromRequest(int $artistId, array $postData, array $files): Artist
    {
        
        $artist = $this->artistRepository->getArtistById($artistId);
        
        if (!$artist) {
            throw new \Exception('Artist not found');
        }
      
        $this->validateArtistData($postData);
      
        $artist = $this->updateArtistFromPostData($artist, $postData);
       
        $artist = $this->handleImageUpload($artist, $files);
        
        $this->artistRepository->update($artist);
        
        return $artist;
    }

    public function deleteArtist(int $artistId): bool
    {
        $artist = $this->artistRepository->getArtistById($artistId);
        
        if (!$artist) {
            throw new \Exception('Artist not found');
        }
        
        return $this->artistRepository->delete($artistId);
    }

    private function validateArtistData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \Exception('Artist name is required');
        }
        
        if (strlen($data['name']) < 2) {
            throw new \Exception('Artist name must be at least 2 characters');
        }
        
        if (strlen($data['name']) > 100) {
            throw new \Exception('Artist name cannot exceed 100 characters');
        }

        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid website URL');
        }
        
        if (!empty($data['spotify_url']) && !filter_var($data['spotify_url'], FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid Spotify URL');
        }
    }

    private function buildArtistFromPostData(array $data): Artist
    {
        $artist = new Artist();
        $artist->name = trim($data['name']);
        $artist->slug = $this->generateSlug($artist->name);
        $artist->bio = !empty($data['bio']) ? trim($data['bio']) : null;
        $artist->featured_quote = !empty($data['featured_quote']) ? trim($data['featured_quote']) : null;
        $artist->website = !empty($data['website']) ? trim($data['website']) : null;
        $artist->spotify_url = !empty($data['spotify_url']) ? trim($data['spotify_url']) : null;
        $artist->youtube_url = !empty($data['youtube_url']) ? trim($data['youtube_url']) : null;
        $artist->soundcloud_url = !empty($data['soundcloud_url']) ? trim($data['soundcloud_url']) : null;
        $artist->press_quote    = !empty($data['press_quote'])    ? trim($data['press_quote'])    : null;
        $artist->collaborations = !empty($data['collaborations']) ? trim($data['collaborations']) : null;
        
        return $artist;
    }

    private function updateArtistFromPostData(Artist $artist, array $data): Artist
    {
        $artist->name = trim($data['name']);
        $artist->slug = $this->generateSlug($artist->name);
        $artist->bio = !empty($data['bio']) ? trim($data['bio']) : null;
        $artist->featured_quote = !empty($data['featured_quote']) ? trim($data['featured_quote']) : null;
        $artist->website = !empty($data['website']) ? trim($data['website']) : null;
        $artist->spotify_url = !empty($data['spotify_url']) ? trim($data['spotify_url']) : null;
        $artist->youtube_url = !empty($data['youtube_url']) ? trim($data['youtube_url']) : null;
        $artist->soundcloud_url = !empty($data['soundcloud_url']) ? trim($data['soundcloud_url']) : null;
        $artist->press_quote    = !empty($data['press_quote'])    ? trim($data['press_quote'])    : null;
        $artist->collaborations = !empty($data['collaborations']) ? trim($data['collaborations']) : null;
        
        
        return $artist;
    }

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

        if ($result['success']) {
            $artist->profile_image = $result['media'];
        } else {
            throw new \Exception('Failed to upload image: ' . $result['error']);
        }

        return $artist;
    }


    private function generateSlug(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
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
     * Upload one or more gallery images for an artist.
     * Creates a gallery for the artist if they don't have one yet.
     *
     * @param int         $artistId
     * @param Artist|null $artist   The current artist (must have gallery_id if it already has a gallery)
     * @param array       $files    The $_FILES['gallery_images'] array (multi-file)
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

            if ($result['success']) {
                $order = $this->artistRepository->getNextGalleryOrder($galleryId);
                $this->artistRepository->addMediaToGallery($galleryId, $result['media']->media_id, $order);
            } else {
                error_log("Gallery image upload failed: " . ($result['error'] ?? 'unknown'));
            }
        }
    }

    /**
     * Remove a gallery image from an artist's gallery (unlinks from gallery, keeps MEDIA record).
     */
    public function removeGalleryImage(int $artistId, int $mediaId): bool
    {
        $artist = $this->artistRepository->getArtistByIdWithGallery($artistId);

        if (!$artist || !$artist->gallery?->gallery_id) {
            return false;
        }

        return $this->artistRepository->removeMediaFromGallery($artist->gallery->gallery_id, $mediaId);
    }

    /**
     * Convert PHP's multi-file upload array structure into a simple list of single-file arrays.
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