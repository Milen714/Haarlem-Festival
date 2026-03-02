<?php
namespace App\Services;

use App\Models\History\Landmark;
use App\Services\Interfaces\ILandmarkService;
use App\Repositories\Interfaces\ILandmarkRepository;
class LandmarkService implements ILandmarkService
{
    private ILandmarkRepository $landmarkRepository;

    public function __construct(ILandmarkRepository $landmarkRepository)
    {
        $this->landmarkRepository = $landmarkRepository;
    }

    public function getLandmarkById(int $landmarkId): ?Landmark
    {
        return $this->landmarkRepository->getLandmarkById($landmarkId);
    }

    public function getAllLandmarks(): array
    {
        return $this->landmarkRepository->getAllLandmarks();
    }
}