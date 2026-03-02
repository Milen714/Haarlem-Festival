<?php

namespace App\Repositories\Interfaces;
use App\Models\History\Landmark;

interface ILandmarkRepository
{
    
    public function getLandmarkById(int $landmarkId): ?Landmark;
    public function getAllLandmarks(): array;

}