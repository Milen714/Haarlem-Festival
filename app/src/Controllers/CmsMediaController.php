<?php

namespace App\Controllers\CMS;

use App\Controllers\BaseController;
use App\Services\MediaService;
use App\Repositories\MediaRepository;

class CmsMediaController extends BaseController
{
    private MediaService $mediaService;

    public function __construct()
    {
        $this->mediaService = new MediaService(new MediaRepository());
    }

    /**
     * Handles the  AJAX image upload from TinyMCE editor to the database
     */
    public function uploadTinyMCE($vars = []): void
    {
        header('Content-Type: application/json');

        try {
            // firstly it Validate;s the files upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->jsonError('No file uploaded or upload error');
                return;
            }

            $altText = $_POST['alt_text'] ?? 'Content image';
            $category = $_POST['category'] ?? 'Home/Content';

            // Validates its category 
            if (!$this->isValidCategory($category)) {
                $this->jsonError('Invalid upload category');
                return;
            }

            // Upload and create media record
            $result = $this->mediaService->uploadAndCreate(
                $_FILES['image'],
                $category,
                $altText
            );

            if ($result['success']) {
                $this->jsonSuccess([
                    'file_path' => $result['media']->file_path,
                    'media_id' => $result['media']->media_id
                ]);
            } else {
                $this->jsonError($result['error']);
            }
        } catch (\Exception $e) {
            $this->jsonError('Upload failed: ' . $e->getMessage());
        }
    }

    private function isValidCategory(string $category): bool
    {
        $allowedCategories = [
            'Home/Content',
            'Home/Sections',
            'Jazz/JazzHome',
            'Jazz/JazzArtist',
            'Dance/DanceHome',
            'Dance/DanceArtist',
            'History/Images',
            'History/Events',
            'Magic/Shows',
            'Magic/Images',
            'Yummy/Restaurants',
            'Yummy/Events'
        ];

        return in_array($category, $allowedCategories);
    }

    /**
     * Send JSON success response
     */
    private function jsonSuccess(array $data): void
    {
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }

    /**
     * Send JSON error response
     */
    private function jsonError(string $message): void
    {
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
}