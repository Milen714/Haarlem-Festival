<?php
namespace App\Models\MusicEvent;

class Genre
{
    public ?int $genre_id = null;
    public ?string $name = null;
    public ?string $description = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->genre_id    = isset($data['genre_id']) ? (int)$data['genre_id'] : null;
        $this->name        = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
    }
}