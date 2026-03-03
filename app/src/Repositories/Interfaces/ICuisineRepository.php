<?php 

namespace App\Src\Repositories\Interfaces;

interface ICuisineRepository{

    public function getCuisines(): array;  
    public function getCuisineByRestaurant(int $restaurantId): array;
}