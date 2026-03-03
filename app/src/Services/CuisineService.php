<?php

namespace App\Src\Services;

use App\Src\Service\Interfaces\ICuisineService;
use App\Src\Repositories\Interfaces\ICuisineRepository;

class CuisineService implements ICuisineService{

    private ICuisineRepository $cuisineRepository;
    
    public function __construct(ICuisineRepository $cuisineRepository)
    {
        $this->cuisineRepository = $cuisineRepository;
    }

    public function getCuisines(): array
    {
        return $this->cuisineRepository->getCuisines();
    }

    public function getCuisineByRestaurant(int $restaurantId): array
    {
        return $this->cuisineRepository->getCuisineByRestaurant($restaurantId);
    }
}