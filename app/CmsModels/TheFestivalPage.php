<?php
namespace App\CmsModels;

use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalSection;
use App\CmsModels\CmsPageModel;

class TheFestivalPage extends CmsPageModel
{
    /** @var TheFestivalSection[] */
    public array $content_sections = [];

    public function __construct() {
        parent::__construct();
    }
    public function addContentSection(TheFestivalSection $section): void {
        $this->content_sections[] = $section;
    }
    public function fromPDOData(array $data): void {
        $this->page_id = $data['page_id'] ?? null;
        $this->page_type = TheFestivalPageType::from($data['page_type']);
        $this->slug = $data['slug'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->hero_media_id = isset($data['hero_media_id']) ? (int)$data['hero_media_id'] : null;
        $this->hero_gallery_id = isset($data['hero_gallery_id']) ? (int)$data['hero_gallery_id'] : null;
        $this->sidebar_html = $data['sidebar_html'] ?? null;
    }

}