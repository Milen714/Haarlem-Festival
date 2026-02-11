<?php
namespace App\CmsModels;
use App\CmsModels\Enums\TheFestivalPageType;

abstract class CmsPageModel
{
    public ?string $page_id = null;
    public TheFestivalPageType $page_type;
    public ?string $slug = null;
    public ?string $title = null;
    public ?int $hero_media_id = null;
    public ?int $hero_gallery_id = null;
    public ?string $sidebar_html = null;

    public function __construct() {
       
    }
    
}