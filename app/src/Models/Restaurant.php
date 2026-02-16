<?php
namespace App\Models;

use App\Models\Media;
use App\Models\Gallery;
use App\Models\Venue;

class Restaurant
{
    public ?int $restaurant_id = null;
    public ?int $event_id = null;
    public ?int $venue_id = null;
    public ?Venue $venue = null;
    public ?int $head_chef_id = null;
    public ?string $name = null;
    public ?string $short_description = null;
    public ?string $welcome_text = null;
    public ?int $price_category = null;
    public ?float $stars = null;
    public ?int $review_count = null;
    public ?Media $main_image = null;
    public ?Gallery $gallery = null;
    public ?string $course_details_html = null;
    public ?string $special_notes_html = null;
    public ?string $website_url = null;
    public ?\DateTime $deleted_at = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->restaurant_id = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $this->event_id = isset($data['event_id']) ? (int)$data['event_id'] : null;
        $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
        $this->head_chef_id = isset($data['head_chef_id']) ? (int)$data['head_chef_id'] : null;
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
        $this->course_details_html = $data['course_details_html'] ?? null;
        $this->special_notes_html = $data['special_notes_html'] ?? null;
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
    }

    /**
     * Get price category as symbols (e.g., €, €€, €€€)
     */
    public function getPriceCategoryDisplay(): string
    {
        return str_repeat('€', $this->price_category ?? 1);
    }
}