<?php 

namespace App\Services\Interfaces;

interface ICuisineService{

    public function getCuisines(): array;  
    public function getCuisineByRestaurant(int $restaurantId): array;
}