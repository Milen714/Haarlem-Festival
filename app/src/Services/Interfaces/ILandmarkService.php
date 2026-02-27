<?php
namespace App\Services\Interfaces;


use App\Models\History\Landmark;

interface ILandmarkService
{
    
    public function getLandmarkById(int $landmarkId): ?Landmark;
    public function getAllLandmarks(): array;

}