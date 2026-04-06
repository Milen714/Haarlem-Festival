<?php

namespace App\Services\Interfaces;
use App\Models\Restaurant;
use App\Models\Yummy\Session;

interface IRestaurantService
{
    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array;
    public function showAllRestaurants(): array;
    public function getRestaurantById(int $id): ?Restaurant;

    public function getRestaurantBySlug(string $slug): ?Restaurant;

    public function getRestaurantsByEventId(int $eventId): array;

    public function createRestaurant(Restaurant $restaurant): int;
    public function updateRestaurant( Restaurant $restaurant): bool;
    public function deleteRestaurant(int $id): bool;

    //Session Crud
    public function getAllSessionsTypes(): array;
    public function getSessionsByRestaurant(int $restaurantId): array;
    public function createSession(Session $session): Session;
    public function deleteSessionsByRestaurant(int $restaurantId): bool;
    public function createFromRequest(array $postData, array $files): Restaurant;
    public function updateFromRequest(int $restaurantId, array $postData, array $files): Restaurant;
     public function uploadRestauratGallery(int $restaurantId, ?Restaurant $restaurant, array $files): void;

    public function removeGalleryImage(int $restaurantId, int $mediaId): bool;
    public function getEvents(): array;
}