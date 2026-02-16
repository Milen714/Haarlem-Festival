<?php

namespace App\Repositories\Interfaces;

use App\Models\Media;
use App\Models\Gallery;

interface IMediaRepository
{
     public function getMediaById(int $id): Media;
     public function getGalleryById(int $galleryId): Gallery;
     public function updateMedia(Media $media): bool;
     public function createMedia(Media $media): bool;
     public function deleteMedia(int $mediaId): bool;
}