<?php
namespace App\CmsModels;

use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalSection;
use App\CmsModels\CmsPageModel;
use App\Models\Media;

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
        $this->title = $data['page_title'] ?? null;
        $this->sidebar_html = $data['sidebar_html'] ?? null;
    }

    public function fromPostData(array $data): void {
        $this->page_id = $data['page_id'] ?? null;
        $this->page_type = TheFestivalPageType::from($data['page_type']);
        $this->slug = $data['slug'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->sidebar_html = $data['content'] ?? ($data['sidebar_html'] ?? null);

        $this->content_sections = [];
        $pageId = isset($data['page_id']) ? (int)$data['page_id'] : null;
        foreach (($data['sections'] ?? []) as $sectionData) {
            $section = new TheFestivalSection();
            $section->fromPostData($sectionData, $pageId);
            $this->addContentSection($section);
        }
    }

}