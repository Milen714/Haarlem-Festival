<?php

namespace App\Services\Interfaces;

interface JazzServiceInterface
{

    public function loadJazzOverview(): array;

    public function loadJazzSchedule(): array;

    public function loadJazzArtistProfile(string $artistSlug): array;
}
