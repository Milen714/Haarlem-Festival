<?php
namespace App\Models\Yummy;

use App\Models\Enums\Allergens;
use App\Models\Media;
use App\Models\Restaurant;
use Dom\Text;

class Dish
{
    public ?int $dish_id = null;
    public ?Restaurant $restaurant = null;
    public ?string $name = null;
    public ?string $description_html = null; 
    public ?Media $image_id = null;
    public ?bool $is_featured = null;
    public ?int $display_order = null;
    public ?bool $is_vegetarian = null;
    public ?bool $is_vegan = null;
    public ?Allergens $allergens = null;
    public ?\DateTime $deleted_at = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->dish_id = isset($data['dish_id']) ? (int)$data['dish_id'] : null;
        $this->restaurant = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $this->name = $data['name'] ?? $data['dish_name'] ?? null;
        $this->description_html = $data['description_html'] ?? $data['dish_description_html'] ?? null;
        $this->is_featured = isset($data['is_featured']) || isset($data['dish_is_featured']) 
            ? (bool)($data['is_featured'] ?? $data['dish_is_featured']) 
            : null;
        $this->display_order = isset($data['display_order']) || isset($data['dish_display_order']) 
        ? (int)($data['display_order'] ?? $data['dish_display_order']) 
        : null;
        $this->is_vegetarian = isset($data['is_vegetarian']) || isset($data['dish_is_vegetarian']) 
            ? (bool)($data['is_vegetarian'] ?? $data['dish_is_vegetarian']) 
            : null;
        $this->is_vegan = isset($data['is_vegan']) || isset($data['restaurant_review_count']) 
            ? (bool)($data['review_count'] ?? $data['restaurant_review_count']) 
            : null;
        $this->deleted_at = isset($data['deleted_at']) ? new \DateTime($data['deleted_at']) : null;

        // Hydrate main image if available
        if (isset($data['image_id']) && $data['image_id'] !== null) {
            $this->image_id = new Media();
            $this->image_id->fromPDOData([
                'media_id' => $data['main_image_id'],
                'file_path' => $data['restaurant_image_path'] ?? null,
                'alt_text' => $data['restaurant_image_alt'] ?? null,
            ]);
        }
    }

}