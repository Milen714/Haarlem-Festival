<?php
namespace App\Services;
use App\Models\Media;
use App\Models\Gallery;
use App\Repositories\Interfaces\IMediaRepository;
use App\Services\Interfaces\IMediaService;

class MediaService implements IMediaService
{
    private IMediaRepository $mediaRepository;

    public function __construct(IMediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
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
    public function createMedia(Media $media): bool { 
        return $this->mediaRepository->createMedia($media); 
    }
}   