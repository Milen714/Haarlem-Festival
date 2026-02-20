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
}