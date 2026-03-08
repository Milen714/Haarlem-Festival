<?php
namespace App\Models;

use App\Models\Media;
use App\Models\Gallery;
use App\Models\Venue;
use App\Models\Cuisine;
use Dom\Text;

class Restaurant
{
    public ?int $restaurant_id = null;
    public ?int $event_id = null;
    public ?int $venue_id = null;
    public ?Venue $venue = null;
    public ?string $chef_name = null;
    public ?string $chef_bio_text = null;
    public ?string $name = null;
    public ?string $short_description = null;
    public ?string $welcome_text = null;
    public ?int $price_category = null;
    public ?float $stars = null;
    public ?int $review_count = null;
    public ?Media $main_image = null;
    public ?Media $chef_img = null;
    public ?Gallery $gallery = null;
    public ?array $cuisines = [];
    public ?string $website_url = null;
    public ?\DateTime $deleted_at = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->restaurant_id = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
        $this->chef_name = $data['chef_name'] ?? $data['restaurant_chef_name'] ?? null;
        $this->chef_bio_text = $data['chef_bio_text'] ?? $data['restaurant_chef_bio_text'] ?? null;
        $this->name = $data['name'] ?? $data['restaurant_name'] ?? null;
        $this->short_description = $data['short_description'] ?? $data['restaurant_short_description'] ?? null;
        $this->welcome_text = $data['welcome_text'] ?? $data['restaurant_welcome_text'] ?? null;
        $this->price_category = isset($data['price_category']) || isset($data['restaurant_price_category']) 
            ? (int)($data['price_category'] ?? $data['restaurant_price_category']) 
            : null;
        $this->stars = isset($data['stars']) || isset($data['restaurant_stars']) 
            ? (float)($data['stars'] ?? $data['restaurant_stars']) 
            : null;
        $this->review_count = isset($data['review_count']) || isset($data['restaurant_review_count']) 
            ? (int)($data['review_count'] ?? $data['restaurant_review_count']) 
            : null;
        $this->website_url = $data['website_url'] ?? $data['restaurant_website_url'] ?? null;
        $this->deleted_at = isset($data['deleted_at']) ? new \DateTime($data['deleted_at']) : null;

        // Hydrate main image if available
        if (isset($data['main_image_id']) && $data['main_image_id'] !== null) {
            $this->main_image = new Media();
            $this->main_image->fromPDOData([
                'media_id' => $data['main_image_id'],
                'file_path' => $data['restaurant_image_path'] ?? null,
                'alt_text' => $data['restaurant_image_alt'] ?? null,
            ]);
        }

        // Hydrate chef image if available
        if (isset($data['chef_img']) && $data['chef_img'] !== null) {
            $this->main_image = new Media();
            $this->main_image->fromPDOData([
                'media_id' => $data['chef_img'],
                'file_path' => $data['restaurant_image_path'] ?? null,
                'alt_text' => $data['restaurant_image_alt'] ?? null,
            ]);
        }

        if (isset($data['cuisine_id']) && $data['cuisine_id'] !== null) {
            $cuisine = new Cuisine();
            $cuisine->fromPDOData([
                'cuisine_id' => $data['cuisine_id'],
                'name' => $data['cuisine_name'] ?? null,
                'description' => $data['cuisine_description'] ?? null,
                'icon_url' => $data['cuisine_icon_url'] ?? null
            ]);

            $this->cuisines[$data['cuisine_id']] = $cuisine;
        }
    }

    /**
     * Get price category as symbols (e.g., €, €€, €€€)
     */
    public function getPriceCategoryDisplay(): string
    {
        return str_repeat('€', $this->price_category ?? 1);
    }
}