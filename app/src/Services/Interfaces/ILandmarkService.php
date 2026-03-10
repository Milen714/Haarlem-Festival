<?php
namespace App\Services\Interfaces;


use App\Models\Landmark;

interface ILandmarkService
{    
    public function getLandmarkById(int $landmarkId): ?Landmark;
    public function getAllLandmarks(): array;
    public function getLandmarkBySlug(string $slug);

    public function createLandmark(array $postData, array $filesData): Landmark;
    public function updateLandmark(int $id, array $postData, array $filesData): Landmark;
    public function deleteLandmark(int $id): void;
}