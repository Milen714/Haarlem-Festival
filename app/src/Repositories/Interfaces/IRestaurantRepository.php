<?php 

namespace App\Repositories\Interfaces;

use App\Models\Restaurant;
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
    public function getAllSessionsTypes(): array;
    public function getSessionsByRestaurant(int $restaurantId): array;
    public function createSession(Session $session): Session;
    public function deleteSessionsByRestaurant(int $restaurantId): bool;

    public function syncRestaurantCuisines(int $restaurantId, $cuisineIds): void;

    public function createGalleryForRestaurant(int $restaurantId, string $title): int;

    public function addMediaToGallery(int $galleryId, int $mediaId, int $displayOrder): bool;

    public function removeMediaFromGallery(int $galleryId, int $mediaId): bool;

    public function getNextGalleryOrder(int $galleryId): int;
    public function getEvents(): array;

}