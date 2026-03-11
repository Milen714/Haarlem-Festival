<?php 

namespace App\Repositories\Interfaces;
use App\Models\Cuisine;

interface ICuisineRepository{

    public function getCuisines(): array;  
    public function getCuisineByRestaurant(int $restaurantId): array;
    public function getCuisineById(int $id): ?Cuisine;
    public function createCuisine(Cuisine $cuisine): int;
    public function updateCuisine(Cuisine $cuisine): bool;
    public function deleteCuisine(int $id): bool;

}