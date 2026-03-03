<?php 

namespace App\Models;

class Cuisine{
    public ?int $cuisine_Id = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $icon = null;

    public function fromPDOData(array $data): void{
        $this->cuisine_Id = $data['cuisine_id'] ?? null;
        $this->name = $data['name'] ?? $data['cuisine_name'] ?? null;
        $this->description = $data['description'] ?? $data['cuisine_description'] ?? null;
        $this->icon = $data['icon_url'] ?? $data['cusine_icon_url'] ?? null;
    }
}