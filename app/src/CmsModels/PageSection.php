<?php 
namespace App\CmsModels;
use App\CmsModels\Enums\TheFestivalSectionPageType;
use App\CmsModels\Enums\SectionType;
use App\Models\Media;
use App\Models\Gallery;

class PageSection
{
    
    public ?int $section_id = null;
    public ?int $page_id = null;
    public SectionType $section_type;
    public ?string $title = null;
    public ?string $content_html = null;
    public ?string $content_html_2 = null;
    public ?Media $media = null;
    public ?string $caption = null;
    public int $display_order;
    public ?string $cta_text = null;
    public ?string $cta_url = null;
    public ?Gallery $gallery = null;

    public function __construct() {
       
    }
    public function fromPDOData(array $data): void {
        $this->section_id = isset($data['section_id']) ? (int)$data['section_id'] : null;
        $this->page_id = isset($data['page_id']) ? (int)$data['page_id'] : null;
        $this->section_type = SectionType::from($data['section_type']);
        $this->title = $data['section_title'] ?? null;
        $this->content_html = $data['content_html'] ?? null;
        $this->content_html_2 = $data['content_html_2'] ?? null;
        $this->caption = $data['caption'] ?? null;
        $this->display_order = isset($data['sec_order']) ? (int)$data['sec_order'] : 0;
        $this->cta_text = $data['cta_text'] ?? null;
        $this->cta_url = $data['cta_url'] ?? null;
    }

    public function fromPostData(array $data, ?int $pageId = null): void {
        $this->section_id = isset($data['section_id']) ? (int)$data['section_id'] : null;
        $this->page_id = $pageId ?? (isset($data['page_id']) ? (int)$data['page_id'] : null);
        $this->section_type = SectionType::from($data['section_type']);
        $this->title = $data['title'] ?? null;
        $this->content_html = $data['content_html'] ?? null;
        $this->content_html_2 = $data['content_html_2'] ?? null;
        $this->media = new Media();
        $this->media->fromPostData(['media_id' => $data['media_id'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'alt_text' => $data['alt_text'] ?? null]);   
        $this->caption = $data['caption'] ?? null;
        $this->display_order = isset($data['display_order']) ? (int)$data['display_order'] : 0;
        $this->cta_text = $data['cta_text'] ?? null;
        $this->cta_url = $data['cta_url'] ?? null;
    }
    public static function findHeroSection(array $sections): ?PageSection {
        foreach ($sections as $section) {
            if ($section->section_type === SectionType::hero_picture || $section->section_type === SectionType::hero_gallery) {
                return $section;
            }
        }
        return null;
    }
}