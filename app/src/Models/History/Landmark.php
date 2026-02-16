<?php
namespace App\Models\History;

class Landmark {
    public int $landmark_id;
    public int $event_id;

    //
    public ?float $latitude;
    public ?float $longitude;
    public int $display_order;

    //
    public string $name;
    public ?string $landmark_title;
    public ?string $short_description;
    public ?int $landmark_image_id;
    public bool $has_detail_page;
    public ?string $landmark_slug;
    public ?string $detail_intro_content;
    public ?string $detail_history_content;
    public ?string $detail_why_content;

    
    public ?int $gallery_id;
    
}