<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Gallery;
use App\Repositories\Interfaces\IMediaRepository;
use App\Services\Interfaces\IMediaService;

class MediaService implements IMediaService
{
    private IMediaRepository $mediaRepository;
    private FileUploadService $fileUploadService;

    public function __construct(IMediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
        $this->fileUploadService = new FileUploadService();
    }

    public function getMediaById(int $id): Media
    {
        return $this->mediaRepository->getMediaById($id);
    }

    public function getGalleryById(int $galleryId): Gallery
    {
        return $this->mediaRepository->getGalleryById($galleryId);
    }

    public function updateMedia(Media $media): bool
    {
        return $this->mediaRepository->updateMedia($media);
    }

    public function createMedia(Media $media): bool
    {
        return $this->mediaRepository->createMedia($media);
    }

    public function uploadAndCreate(array $file, string $category, string $altText): array
    {
        // Upload the file
        $uploadResult = $this->fileUploadService->upload($file, $category);

        if (!$uploadResult['success']) {
            return ['success' => false, 'media' => null, 'error' => $uploadResult['error']];
        }

        // Create media record (removed media_type - not in schema)
        $media = new Media();
        $media->file_path = $uploadResult['file_path'];
        $media->alt_text = $altText;

        $created = $this->mediaRepository->createMedia($media);

        if (!$created) {
            // Rollback: delete uploaded file if database insert fails
            $this->fileUploadService->delete($uploadResult['file_path']);
            return ['success' => false, 'media' => null, 'error' => 'Failed to create database record'];
        }

        return ['success' => true, 'media' => $media, 'error' => null];
    }

    public function replaceMedia(int $mediaId, array $file, string $category, string $altText): array
    {
        // Get existing media
        $existingMedia = $this->mediaRepository->getMediaById($mediaId);
        if (!$existingMedia || !$existingMedia->media_id) {
            return ['success' => false, 'error' => 'Media not found'];
        }

        $oldFilePath = $existingMedia->file_path;

        // Upload new file
        $uploadResult = $this->fileUploadService->upload($file, $category);

        if (!$uploadResult['success']) {
            return ['success' => false, 'error' => $uploadResult['error']];
        }

        // Update database record
        $existingMedia->file_path = $uploadResult['file_path'];
        $existingMedia->alt_text = $altText;

        $updated = $this->mediaRepository->updateMedia($existingMedia);

        if (!$updated) {
            // Rollback: delete new file if database update fails
            $this->fileUploadService->delete($uploadResult['file_path']);
            return ['success' => false, 'error' => 'Failed to update database'];
        }

        // Delete old file after successful update
        if ($oldFilePath) {
            $this->fileUploadService->delete($oldFilePath);
        }

        return ['success' => true, 'error' => null];
    }

    public function deleteMedia(int $mediaId): bool
    {
        $media = $this->mediaRepository->getMediaById($mediaId);
        if (!$media || !$media->media_id) {
            return false;
        }

        // Delete file first
        if ($media->file_path) {
            $this->fileUploadService->delete($media->file_path);
        }

        // Then delete database record
        return $this->mediaRepository->deleteMedia($mediaId);
    }
}