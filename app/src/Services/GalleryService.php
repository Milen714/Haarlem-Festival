<?php

namespace App\Service;

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
        // Mapeamos los inputs del form a un orden lógico
        $slots = [
            'img_intro'     => 0,
            'img_history'   => 1,
            'img_practical' => 2
        ];

        foreach ($slots as $inputName => $order) {
            // Verificamos si hay un archivo nuevo en este slot
            if (isset($filesData[$inputName]) && $filesData[$inputName]['error'] === UPLOAD_ERR_OK) {
                
                // ¿Teníamos un ID previo para esta posición?
                $existingMediaId = $postData[$inputName . '_id'] ?? null;

                if ($existingMediaId && $existingMediaId !== '') {
                    // REEMPLAZAR: Actualiza el file_path y borra el archivo físico anterior
                    $this->mediaService->replaceMedia(
                        (int)$existingMediaId, 
                        $filesData[$inputName], 
                        'Landmarks', 
                        "Landmark Section Image $order"
                    );
                } else {
                    // NUEVO: Crea registro en MEDIA y lo vincula a la galería
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