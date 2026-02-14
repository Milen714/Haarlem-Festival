<?php
namespace App\CmsModels;
use App\CmsModels\Enums\PageType;
use App\Models\Media;
use App\Models\Enums\EventType;
use App\Models\EventCategory;

abstract class CmsPageModel
{
    public ?string $page_id = null;
    public PageType $page_type;
    public ?string $slug = null;
    public ?string $title = null;
    public ?string $sidebar_html = null;
    public ?EventCategory $event_category = null;
    

    public function __construct() {
       
    }
    
}