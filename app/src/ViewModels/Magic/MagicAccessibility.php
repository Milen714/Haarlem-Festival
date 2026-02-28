<?php
namespace App\ViewModels\Magic;
use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\SectionType;

class MagicAccessibility{
    public Page $page;
    public ?PageSection $heroSection = null;
    
     /** @var PageSection[] $introSections */
    public array $introSections = [];

      /** @var PageSection[] $accessibilitySections */
    public array $accessibilitySections = [];

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->heroSection = PageSection::findHeroSection($page->content_sections);
        foreach ($page->content_sections as $section) {
            $title = $section->title ?? '';
            
            if (str_starts_with($title, 'Intro')) {
                $this->introSections[] = $section;
            }
            if (str_starts_with($title, 'Accessibility')) {
                $this->accessibilitySections[] = $section;
            }
        }
    }
    public function displaySections(array $sections): void {
        foreach ($sections as $section) {
            switch ($section->section_type) {
                case SectionType::article:
                    include '../Views/Magic/Components/MagicParagraph.php';
                    break;
                case SectionType::image_left:
                case SectionType::image_right:
                    $this->displayImageSection($section);
                    break;
                case SectionType::accordion_group:
                     include '../Views/Magic/Components/MagicAccordion.php';
                    break;
            }
        }
    }
    public function imageSectionStyle(PageSection $section): array
    {
        switch ($section->section_type) {
            case SectionType::image_bottom:
                return ['cardStyle'=>"magic_image_article_vertical_reverse", 'imageStyle'=>"magic_article_image_big" ];
                break;
            case SectionType::image_top:
                return ['cardStyle'=>"flex-col md:flex-row" ,'imageStyle'=>""];
                break;
            case SectionType::image_left:
                return ['cardStyle'=>"magic_image_article" ,'imageStyle'=>"magic_article_image_small"];
                break;
            case SectionType::image_right:
                return ['cardStyle'=>"magic_image_article_reverse" ,'imageStyle'=>"magic_article_image_small"];
            default:
                return ['cardStyle'=>"flex-col md:flex-row" ,'imageStyle'=>""];
        }
    }
    public function displayImageSection(PageSection $section): void
    {
        $imageSectionStyle = $this->imageSectionStyle($section);
        include '../Views/Magic/Components/MagicArticleWithImage.php';
    }
}