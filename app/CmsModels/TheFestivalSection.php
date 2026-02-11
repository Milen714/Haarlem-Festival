<?php 
namespace App\CmsModels;
use App\CmsModels\Enums\TheFestivalSectionPageType;
use App\CmsModels\Enums\SectionType;

class TheFestivalSection
{
    
    public ?int $section_id = null;
    public ?int $page_id = null;
    public SectionType $section_type;
    public ?string $title = null;
    public ?string $content_html = null;
    public ?int $media_id = null;
    public ?string $caption = null;
    public int $display_order;

    public function __construct() {
       
    }
    public function fromPDOData(array $data): void {
        $this->section_id = isset($data['section_id']) ? (int)$data['section_id'] : null;
        $this->page_id = isset($data['page_id']) ? (int)$data['page_id'] : null;
        $this->section_type = SectionType::from($data['section_type']);
        $this->title = $data['title'] ?? null;
        $this->content_html = $data['content_html'] ?? null;
        $this->media_id = isset($data['media_id']) ? (int)$data['media_id'] : null;
        $this->caption = $data['caption'] ?? null;
        $this->display_order = isset($data['sec_order']) ? (int)$data['sec_order'] : 0;
    }
}