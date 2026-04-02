<?php

namespace App\Services;

use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\ILogService;
use App\Repositories\VenueRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Models\Venue;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ApplicationException;

class VenueService implements IVenueService
{
    private IVenueRepository $venueRepository;
    private IMediaService $mediaService;
    private ILogService $logService;

    /**
     * Wires up the VenueRepository for data access and MediaService for handling
     * venue image uploads and replacements.
     */
    public function __construct() {
        $this->venueRepository = new VenueRepository();
        $this->mediaService = new MediaService();
        $this->logService = new LogService();
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
            throw new ApplicationException('Failed to create venue in database');
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
            throw new ApplicationException('Failed to update venue in database');
        }

        return $venue;
    }

    public function deleteVenue(int $venueId): bool
    {
        $venue = $this->venueRepository->getVenueById($venueId);

        if (!$venue) {
            throw new ResourceNotFoundException('Venue not found.');
        }

        return $this->venueRepository->delete($venueId);
    }

    /**
     * Validates name (min 2 chars), required address/city, numeric non-negative capacity, and email format.
     */
    private function validateVenueData(array $data): void
    {
        if (empty($data['name'])) {
            throw new ValidationException('Venue name is required.');
        }

        if (strlen($data['name']) < 2) {
            throw new ValidationException('Venue name must be at least 2 characters.');
        }

        if (empty($data['street_address'])) {
            throw new ValidationException('Street address is required.');
        }

        if (empty($data['city'])) {
            throw new ValidationException('City is required.');
        }

        if (isset($data['capacity']) && $data['capacity'] !== '' && !is_numeric($data['capacity'])) {
            throw new ValidationException('Capacity must be a number.');
        }

        if (isset($data['capacity']) && $data['capacity'] !== '' && (int)$data['capacity'] < 0) {
            throw new ValidationException('Capacity cannot be negative.');
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email address.');
        }
    }

    /**
     * Skips silently when no file is present or the upload errored.
     * Calls replaceMedia() when the venue already has a media_id (update path),
     * otherwise uploadAndCreate() (create path). Throws on failure.
     */
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
            $this->logService->exception('Venue', $e, ['venue' => $venue->name]);
            throw new \Exception('Failed to upload venue image: ' . $e->getMessage());
        }

        return $venue;
    }
}
