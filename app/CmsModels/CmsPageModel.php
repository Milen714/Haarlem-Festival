<?php
namespace App\CmsModels;
use App\CmsModels\Enums\TheFestivalPageType;
use App\Models\Media;

abstract class CmsPageModel
{
    public ?string $page_id = null;
    public TheFestivalPageType $page_type;
    public ?string $slug = null;
    public ?string $title = null;
    public ?string $sidebar_html = null;

    public function __construct() {
       
    }
    
}