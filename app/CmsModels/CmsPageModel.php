<?php
namespace App\CmsModels;
use App\CmsModels\Enums\PageType;
use App\Models\Media;

abstract class CmsPageModel
{
    public ?string $page_id = null;
    public PageType $page_type;
    public ?string $slug = null;
    public ?string $title = null;
    public ?string $sidebar_html = null;

    public function __construct() {
       
    }
    
}