<?php 

namespace App\Repositories\Interfaces;

use App\Models\Restaurant;

interface IRestaurantRepository
{
    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array;
    public function showAllRestaurants(): array;
    public function getRestaurantById(int $id): ?Restaurant;

    public function getRestaurantBySlug(string $slug): ?Restaurant;

    public function getRestaurantsByEventId(int $eventId): array;

    public function createRestaurant(Restaurant $restaurant): int;
    public function updateRestaurant( Restaurant $restaurant): bool;
    public function deleteRestaurant(int $id): bool;
}