<?php 

namespace App\Services;

use App\Models\Restaurant;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService implements IRestaurantService
{
    private IRestaurantRepository $restaurantRepository;

    public function __construct(IRestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    public function getAllRestaurants(): array
    {
        return $this->restaurantRepository->getAllRestaurants();
    }

    public function getRestaurantById(int $id): ?Restaurant
    {
        return $this->restaurantRepository->getRestaurantById($id);
    }

    public function getRestaurantBySlug(string $slug): ?Restaurant
    {
        return $this->restaurantRepository->getRestaurantBySlug($slug);
    }

    public function getRestaurantsByEventId(int $eventId): array
    {
        return $this->restaurantRepository->getRestaurantsByEventId($eventId);
    }

    public function createRestaurant(Restaurant $restaurant): int
    {
        return $this->restaurantRepository->createRestaurant($restaurant);
    }

    public function updateRestaurant(Restaurant $restaurant): bool
    {
        return $this->restaurantRepository->updateRestaurant($restaurant);
    }

    public function deleteRestaurant(int $id): bool
    {
        return $this->restaurantRepository->deleteRestaurant($id);
    }
}