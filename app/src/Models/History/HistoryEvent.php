<?php
namespace App\Models\History;

class HistoryEvent {
    public int $event_id;

    //
    public ?string $hero_title;
    public ?string $hero_content;
    public ?int $hero_image_id;
    public ?string $intro_content;
    public ?string $good_to_know_text;
    public ?string $souvenir_info;

    //
    public ?string $start_location;
    public ?string $tour_type;
    public ?string $duration;
    
    
    public ?int $gallery_id;

}