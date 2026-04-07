<?php

namespace App\Services;

use App\Repositories\LandmarkRepository;
use App\Services\MediaService;
use App\Models\Landmark;
use App\Models\Gallery;
use App\Services\GalleryService;
use App\Services\Interfaces\ILandmarkService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\IGalleryService;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;

class LandmarkService implements ILandmarkService
{
    private LandmarkRepository $landmarkRepository;
    private IMediaService $mediaService;
    private IGalleryService $galleryService;

    public function __construct()
    {
        $this->landmarkRepository = new LandmarkRepository();
        $this->mediaService = new MediaService();
        $this->galleryService = new GalleryService();
    }

    public function getAllLandmarks(): array
    {
        return $this->landmarkRepository->getAll();
    }

    public function getAllLandmarksWithDetails(): array
    {
        return $this->landmarkRepository->getAllWithDetails();
    }

    public function getFeaturedLandmarks(): array
    {
        return $this->landmarkRepository->getFeatured();
    }

    public function getLandmarkById(int $id): ?Landmark
    {
        return $this->landmarkRepository->getById($id);
    }

    public function getLandmarkBySlug(string $slug): ?Landmark
    {
        return $this->landmarkRepository->getBySlug($slug);
    }

    private function mapLandmarkData(array $postData, string $slug, ?Landmark $landmark = null): Landmark
    {
        if ($landmark === null) {
            $landmark = new Landmark();
        }

        $landmark->name = trim($postData['name']);
        $landmark->event_id = isset($postData['event_id']) ? (int)$postData['event_id'] : 2;
        $landmark->landmark_slug = $slug;
        $landmark->short_description = $postData['short_description'] ?? null;

        $landmark->intro_title = $postData['intro_title'] ?? null;
        $landmark->intro_content = $postData['intro_content'] ?? null;
        $landmark->why_visit_title = $postData['why_visit_title'] ?? null;
        $landmark->why_visit_content = $postData['why_visit_content'] ?? null;
        $landmark->detail_history_title = $postData['detail_history_title'] ?? null;
        $landmark->detail_history_content = $postData['detail_history_content'] ?? null;

        $landmark->display_order = isset($postData['display_order']) ? (int)$postData['display_order'] : 0;
        $landmark->latitude = isset($postData['latitude']) && $postData['latitude'] !== '' ? (float)$postData['latitude'] : null;
        $landmark->longitude = isset($postData['longitude']) && $postData['longitude'] !== '' ? (float)$postData['longitude'] : null;
        $landmark->is_featured = isset($postData['is_featured']) && $postData['is_featured'] === '1';
        $landmark->home_cta = $postData['home_cta'] ?? null;

        return $landmark;
    }

    /**
     * Creates a new landmark with the given form data and uploaded files.
     * Generates a URL slug from the name, inserts the record, creates a gallery,
     * and handles the main image upload.
     *
     * @param  array   $postData   Validated POST fields (name required)
     * @param  array   $filesData  Uploaded files from $_FILES
     * @return Landmark            The newly created landmark
     * @throws ValidationException if the name field is empty
     */
    public function createLandmark(array $postData, array $filesData): Landmark
    {
        if (empty($postData['name'])) {
            throw new ValidationException("The landmark name is required.");
        }

        $slug     = $this->generateSlug($postData['name']);
        $landmark = $this->mapLandmarkData($postData, $slug);
        $landmark = $this->landmarkRepository->insert($landmark);

        $galleryId = $this->ensureGallery($landmark);
        $this->galleryService->handleSectionUploads($galleryId, $postData, $filesData);
        $this->handleMainImageUpload($landmark, $filesData, $postData['name']);

        return $landmark;
    }

    /**
     * Updates an existing landmark identified by $id.
     * Re-generates the slug from the updated name and replaces or adds images.
     *
     * @param  int     $id         Landmark ID to update
     * @param  array   $postData   Updated POST fields (name required)
     * @param  array   $filesData  Uploaded files from $_FILES
     * @return Landmark            The updated landmark
     * @throws ResourceNotFoundException if no landmark exists with that ID
     * @throws ValidationException       if the name field is empty
     */
    public function updateLandmark(int $id, array $postData, array $filesData): Landmark
    {
        $existingLandmark = $this->landmarkRepository->getById($id);

        if (!$existingLandmark) {
            throw new ResourceNotFoundException("Landmark not found.");
        }

        if (empty($postData['name'])) {
            throw new ValidationException("The landmark name is required.");
        }

        $galleryId = $this->ensureGallery($existingLandmark);
        $this->galleryService->handleSectionUploads($galleryId, $postData, $filesData);
        $this->handleMainImageUpload($existingLandmark, $filesData, $postData['name']);

        $updatedLandmark = $this->mapLandmarkData($postData, $this->generateSlug($postData['name']), $existingLandmark);

        return $this->landmarkRepository->update($updatedLandmark);
    }

    public function deleteLandmark(int $id): void
    {
        $this->landmarkRepository->delete($id);
    }

    private function ensureGallery(Landmark $landmark): int
    {
        if ($landmark->gallery) {
            return $landmark->gallery->gallery_id;
        }
        $galleryId = $this->landmarkRepository->createGalleryForLandmark($landmark->landmark_id);
        $gallery = new Gallery();
        $gallery->gallery_id = $galleryId;
        $landmark->gallery = $gallery;
        return $galleryId;
    }

    private function handleMainImageUpload(Landmark $landmark, array $filesData, string $name): void
    {
        if (empty($filesData['main_image']['tmp_name'])) {
            return;
        }
        if ($landmark->main_image_id?->media_id) {
            $this->mediaService->replaceMedia(
                $landmark->main_image_id->media_id,
                $filesData['main_image'],
                'History/Landmarks',
                $name
            );
        } else {
            $result = $this->mediaService->uploadAndCreate($filesData['main_image'], 'History/Landmarks', $name);
            if ($result['success']) {
                $this->landmarkRepository->updateMainImage($landmark->landmark_id, $result['media']->media_id);
            }
        }
    }

    /**
     * Converts a string to a lowercase URL-friendly slug.
     * Strips accents, replaces spaces and special characters with hyphens.
     *
     * @param  string $text  Input text (e.g. landmark name)
     * @return string        Slug (e.g. "grote-kerk")
     */
    private function generateSlug(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = strtolower($text);

        return empty($text) ? 'n-a' : $text;
    }
}
