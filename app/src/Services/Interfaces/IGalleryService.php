<?php
namespace App\Services\Interfaces;

interface IGalleryService
{
    public function handleSectionUploads(int $galleryId, array $postData, array $filesData): void;
}