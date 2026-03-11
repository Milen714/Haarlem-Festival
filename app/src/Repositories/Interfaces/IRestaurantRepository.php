<?php 

namespace App\Repositories\Interfaces;

use App\Models\Restaurant;
use App\Models\Yummy\Dish;
use App\Models\Yummy\Session;

interface IRestaurantRepository
{
    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array;
    public function showAllRestaurants(): array;
    public function getRestaurantById(int $id): ?Restaurant;

    public function getRestaurantBySlug(string $slug): ?Restaurant;

    public function getRestaurantsByEventId(int $eventId): array;

    public function createRestaurant(Restaurant $restaurant): int;
    public function updateRestaurant(Restaurant $restaurant): bool;
    public function deleteRestaurant(int $id): bool;

    //Session Crud
    public function getSessions(): array;  
    public function getAllSessionsTypes(): array;
    public function getSessionsByRestaurant(int $restaurantId): array;
    public function getSessionById(int $id): ?Session;
    public function createSession(Session $session): int;
    public function updateSession(Session $session): bool;
    public function deleteSession(int $id): bool;

    //Dish Crud

     public function getDishes(): array;  
    public function getDishessByRestaurant(int $restaurantId): array;
    public function getDishById(int $id): ?Dish;
    public function createDish(Dish $dish): int;
    public function updateDish(Dish $dish): bool;
    public function deleteDish(int $id): bool;

}