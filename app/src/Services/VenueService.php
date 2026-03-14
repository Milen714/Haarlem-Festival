<?php

namespace App\Services;

use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IMediaService;
use App\Repositories\VenueRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Models\Venue;

class VenueService implements IVenueService
{
    private IVenueRepository $venueRepository;
    private IMediaService $mediaService;

    public function __construct() {
        $this->venueRepository = new VenueRepository();
        $this->mediaService = new MediaService();
    }

    public function getVenuesByEventId(int $eventId): array
    {
        return $this->venueRepository->getVenuesByEventId($eventId);
    }

    public function getVenueById(int $venueId): ?Venue
    {
        return $this->venueRepository->getVenueById($venueId);
    }

    public function getAllVenues(): array
    {
        return $this->venueRepository->getAllVenues();
    }

    public function createFromRequest(array $postData, array $files): Venue
    {
        $this->validateVenueData($postData);
        
        $venue = Venue::createFromPostData($postData);
       
        $venue = $this->handleImageUpload($venue, $files);
        
        $success = $this->venueRepository->create($venue);
        
        if (!$success) {
            throw new \Exception('Failed to create venue in database');
        }
        
        return $venue;
    }

    public function updateFromRequest(int $venueId, array $postData, array $files): Venue
    {
        $venue = $this->venueRepository->getVenueById($venueId);
        
        if (!$venue) {
            throw new \Exception('Venue not found');
        }
     
        $this->validateVenueData($postData);
        
        $venue->applyPostData($postData);
       
        $venue = $this->handleImageUpload($venue, $files);
        
        $success = $this->venueRepository->update($venue);
        
        if (!$success) {
            throw new \Exception('Failed to update venue in database');
        }
        
        return $venue;
    }

    public function deleteVenue(int $venueId): bool
    {
        $venue = $this->venueRepository->getVenueById($venueId);
        
        if (!$venue) {
            throw new \Exception('Venue not found');
        }
        
        return $this->venueRepository->delete($venueId);
    }

    private function validateVenueData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \Exception('Venue name is required');
        }
        
        if (strlen($data['name']) < 2) {
            throw new \Exception('Venue name must be at least 2 characters');
        }
        
        if (empty($data['street_address'])) {
            throw new \Exception('Street address is required');
        }
        
        if (empty($data['city'])) {
            throw new \Exception('City is required');
        }

        if (isset($data['capacity']) && $data['capacity'] !== '' && !is_numeric($data['capacity'])) {
            throw new \Exception('Capacity must be a number');
        }

        if (isset($data['capacity']) && $data['capacity'] !== '' && (int)$data['capacity'] < 0) {
            throw new \Exception('Capacity cannot be negative');
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email address');
        }
    }

    private function handleImageUpload(Venue $venue, array $files): Venue
    {
        if (!isset($files['venue_image']) || $files['venue_image']['error'] !== UPLOAD_ERR_OK) {
            return $venue;
        }

        $isUpdate = $venue->venue_image !== null && isset($venue->venue_image->media_id);
        
        try {
            if ($isUpdate) {
                
                $result = $this->mediaService->replaceMedia(
                    $venue->venue_image->media_id,
                    $files['venue_image'],
                    'Venues',
                    $venue->name . ' venue'
                );
            } else {
                
                $result = $this->mediaService->uploadAndCreate(
                    $files['venue_image'],
                    'Venues',
                    $venue->name . ' venue'
                );
            }

            if ($result['success'] && isset($result['media'])) {
                $venue->venue_image = $result['media'];
            } else {
                $errorMsg = $result['error'] ?? 'Unknown error';
                throw new \Exception('Failed to upload image: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            error_log("Image upload error for venue '{$venue->name}': " . $e->getMessage());
            throw new \Exception('Failed to upload venue image: ' . $e->getMessage());
        }

        return $venue;
    }
}