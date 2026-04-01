<?php

namespace App\Controllers\CMS;

use App\Framework\BaseController;
use App\Services\MediaService;
use App\Services\Interfaces\IMediaService;

class CmsMediaController extends BaseController
{
    private IMediaService $mediaService;

    public function __construct()
    {
        $this->mediaService = new MediaService();
    }

    /**
     * Handles the  AJAX image upload from TinyMCE editor to the database
     */
    public function uploadTinyMCE($vars = []): void
    {
        try {
            // firstly it Validate;s the files upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->sendErrorResponse('No file uploaded or upload error', 400);
                return;
            }

            $altText = $_POST['alt_text'] ?? 'Content image';
            $category = $_POST['category'] ?? 'Home/Content';

            // Validates its category 
            if (!$this->isValidCategory($category)) {
                $this->sendErrorResponse('Invalid upload category', 400);
                return;
            }

            // Upload and create media record
            $result = $this->mediaService->uploadAndCreate(
                $_FILES['image'],
                $category,
                $altText
            );

            if ($result['success']) {
                $this->sendSuccessResponse(array_merge(['success' => true], [
                    'file_path' => $result['media']->file_path,
                    'media_id' => $result['media']->media_id
                ]), 200);
            } else {
                $this->sendErrorResponse($result['error'], 400);
            }
        } catch (\Exception $e) {
            $this->sendErrorResponse('Upload failed: ' . $e->getMessage(), 500);
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
}