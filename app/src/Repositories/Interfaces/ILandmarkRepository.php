<?php

namespace App\Repositories\Interfaces;
use App\Models\Landmark;

interface ILandmarkRepository
{
    public function getAll(): array;
    public function getAllWithDetails(): array;
    public function getById(int $id): ?Landmark;
    public function getBySlug(string $slug): ?Landmark;
    
    public function delete(int $id): bool;
    public function update(Landmark $landmark): Landmark;
    public function insert(Landmark $landmark): Landmark;

    public function getFeatured(): array;
    public function updateMainImage(int $landmarkId, int $mediaId): void;
}