<?php 

namespace App\Repositories\Interfaces;

interface ICuisineRepository{

    public function getCuisines(): array;  
    public function getCuisineByRestaurant(int $restaurantId): array;
}