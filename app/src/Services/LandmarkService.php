<?php

namespace App\Services;

use App\Repositories\LandmarkRepository;
use App\Services\MediaService; 
use App\Models\History\Landmark;

class LandmarkService
{
    private LandmarkRepository $landmarkRepository;
    private MediaService $mediaService;

    public function __construct()
    {
        $this->landmarkRepository = new LandmarkRepository();
        //$this->mediaService = new MediaService(); 
    }

    public function getAllLandmarks(): array
    {
        return $this->landmarkRepository->getAll();
    }

    public function getLandmarkBySlug(string $slug)
    {
        return $this->landmarkRepository->getBySlug($slug);
    }

    private function mapLandmarkData(array $postData, string $slug, ?Landmark $landmark = null): Landmark
    {
        if ($landmark === null) {
            $landmark = new Landmark();
        }

        $landmark->name = trim($postData['name']);
        $landmark->landmark_slug = $slug;
        $landmark->short_description = $postData['short_description'] ?? null;
        
        $landmark->intro_title = $postData['intro_title'] ?? null;
        $landmark->intro_content = $postData['intro_content'] ?? null;
        $landmark->why_visit_title = $postData['why_visit_title'] ?? null;
        $landmark->why_visit_content = $postData['why_visit_content'] ?? null;
        $landmark->detail_history_title = $postData['detail_history_title'] ?? null;
        $landmark->detail_history_content = $postData['detail_history_content'] ?? null;
        
        $landmark->display_order = isset($postData['display_order']) ? (int)$postData['display_order'] : 0;

        return $landmark;
    }

    public function createLandmark(array $postData, array $filesData): Landmark
    {
        //validation fo empty name
        if (empty($postData['name'])) {
            throw new \Exception("The landmark name is required.");
        }

        $slug = $this->generateSlug($postData['name']); //generate new slug

        if ($this->landmarkRepository->getBySlug($slug)) { //avoid duplicate slug 
            $slug .= '-' . rand(100, 999);
        }

        $landmark = $this->mapLandmarkData($postData, $slug);

        return $this->landmarkRepository->insert($landmark);
    }

    public function updateLandmark(string $slug, array $postData, array $filesData): Landmark
    {
        //search the existing landmark
        $existingLandmark = $this->landmarkRepository->getBySlug($slug);

        if (!$existingLandmark) {
            throw new \Exception("Landmark not found.");
        }

        //name validation
        if (empty($postData['name'])) {
            throw new \Exception("The landmark name is required.");
        }

        $newSlug = $slug; 

        $updatedLandmark = $this->mapLandmarkData($postData, $slug, $existingLandmark);

        return $this->landmarkRepository->update($updatedLandmark);
    }

    public function deleteLandmark(string $slug): void
    {
        $existingLandmark = $this->landmarkRepository->getBySlug($slug);
        
        if (!$existingLandmark) {
            throw new \Exception("Landmark not found.");
        }

        $this->landmarkRepository->delete($existingLandmark->id);
    }

    //convert a normal name into a landmark slug
    private function generateSlug(string $text): string
    {
        //if a character is not numbers or letters is changed to a -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        //convert special characters
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        //remove - at the beggining or end 
        $text = trim($text, '-');

        //convert everything to lower case 
        $text = strtolower($text);

        return empty($text) ? 'n-a' : $text;
    }
}