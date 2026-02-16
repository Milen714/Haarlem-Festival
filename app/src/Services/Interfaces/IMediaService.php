<?php 
namespace App\Services\Interfaces;
use App\Models\Media;
use App\Models\Gallery;

interface IMediaService
{
    public function getMediaById(int $id): Media;
     public function getGalleryById(int $galleryId): Gallery;
     public function updateMedia(Media $media): bool;
     public function createMedia(Media $media): bool;
}