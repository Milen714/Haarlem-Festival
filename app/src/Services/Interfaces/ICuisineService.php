<?php 

namespace App\Services\Interfaces;
use App\Models\Cuisine;

interface ICuisineService{

    public function getCuisines(): array;  
    public function getCuisineById(int $id): ?Cuisine;
    public function createCuisine(Cuisine $cuisine): int;
    public function updateCuisine(Cuisine $cuisine): bool;
    public function deleteCuisine(int $id): bool;
    public function createCuisineFromRequest(array $postData);
    public function updateCuisineFromRequest(int $id, array $postData): Cuisine;
}