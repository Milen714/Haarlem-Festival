<?php

namespace App\Services;

use App\Services\Interfaces\IMediaService;
use App\Repositories\GalleryRepository;

class GalleryService {
    private IMediaService $mediaService;
    private GalleryRepository $galleryRepository;

    public function __construct(IMediaService $mediaService, GalleryRepository $galleryRepository) {
        $this->mediaService = $mediaService;
        $this->galleryRepository = $galleryRepository;
    }

    public function handleSectionUploads(int $galleryId, array $postData, array $filesData): void {
        $slots = [
            'img_intro'     => 0,
            'img_history'   => 1,
            'img_practical' => 2
        ];

        foreach ($slots as $inputName => $order) {
            if (isset($filesData[$inputName]) && $filesData[$inputName]['error'] === UPLOAD_ERR_OK) {
                
                $existingMediaId = $postData[$inputName . '_id'] ?? null;

                if ($existingMediaId && $existingMediaId !== '') {
                    $this->mediaService->replaceMedia(
                        (int)$existingMediaId, 
                        $filesData[$inputName], 
                        'Landmarks', 
                        "Landmark Section Image $order"
                    );
                } else {
                    $result = $this->mediaService->uploadAndCreate(
                        $filesData[$inputName], 
                        'Landmarks', 
                        "Landmark Section Image $order"
                    );

                    if ($result['success']) {
                        $this->galleryRepository->addMediaToGallery($galleryId, $result['media']->media_id, $order);
                    }
                }
            }
        }
    }
}