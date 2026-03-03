<?php 

namespace App\Src\Service\Interfaces;

interface ICuisineService{

    public function getCuisines(): array;  
    public function getCuisineByRestaurant(int $restaurantId): array;
}