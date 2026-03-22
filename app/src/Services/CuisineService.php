<?php

namespace App\Services;

use App\Models\Cuisine;
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

    public function getCuisineById(int $id): ?Cuisine
    {
        return $this->cuisineRepository->getCuisineById($id);
    }

    public function createCuisine(Cuisine $cuisine): int
    {
        return $this->cuisineRepository->createCuisine($cuisine);
    }

    public function updateCuisine(Cuisine $cuisine): bool
    {
        return $this->cuisineRepository->updateCuisine($cuisine);
    }

    public function deleteCuisine(int $id): bool
    {
        return $this->cuisineRepository->deleteCuisine($id);
    }

    public function createCuisineFromRequest(array $postData){
        $cuisine = new Cuisine();
        $cuisine = $this->processCuisineRequest($cuisine, $postData);
        return $this->cuisineRepository->createCuisine($cuisine);
    }

    public function updateCuisineFromRequest(int $id, array $postData): Cuisine{
        $cuisine = $this->cuisineRepository->getCuisineById($id);

        if(!$cuisine){
            throw new \Exception('Cuisine not Found');
        }
        
        $cuisine = $this->processCuisineRequest($cuisine, $postData);

        $this->cuisineRepository->updateCuisine($cuisine);
        return $cuisine;
    }

    private function processCuisineRequest(Cuisine $cuisine, array $data): Cuisine
    {
        $cuisine->name = trim($data['name'] ?? '');
        $cuisine->description = !empty($data['description']) 
            ? trim($data['description']) 
            : null;

        $cuisine->icon = !empty($data['icon_url']) 
            ? trim($data['icon_url']) 
            : null;

        return $cuisine;
    }
}