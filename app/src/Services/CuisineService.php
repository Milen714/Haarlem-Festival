<?php

namespace App\Services;

use App\Services\Interfaces\ICuisineService;
use App\Repositories\CuisineRepository;
use App\Repositories\Interfaces\ICuisineRepository;

class CuisineService implements ICuisineService{

    private ICuisineRepository $cuisineRepository;
    
    public function __construct()
    {
        $this->cuisineRepository = new CuisineRepository();
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