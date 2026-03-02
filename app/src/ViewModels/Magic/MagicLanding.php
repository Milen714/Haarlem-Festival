<?php
namespace App\ViewModels\Magic;
use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\SectionType;
class MagicLanding
{
    public Page $page;
    public ?PageSection $heroSection = null;

    /** @var PageSection[] $characterSections */
    public array $characterSections = [];

    /** @var PageSection $StampBookSection */
    public ?PageSection $stampBookSection = null;
    
    /** @var PageSection[] $gameSections */
    public array $gameSections = [];

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->heroSection = PageSection::findHeroSection($page->content_sections);
        foreach ($page->content_sections as $section) {
            $title = $section->title ?? '';
            $contentHtml = $section->content_html ?? '';

            if (
                str_starts_with($title, 'Characters -')
                && !str_contains($contentHtml, 'Lorentz Formula')
            ) {
                $this->characterSections[] = $section;
            }

            if (str_starts_with($title, 'Game -')) {
                $this->gameSections[] = $section;
            }

            if (str_contains($title, 'Stamp Book')) {
                $this->stampBookSection = $section;
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
    public function displayCharacterSections($characterSections): void
    {
        foreach ($characterSections as $section) {
            if ($section->section_type === SectionType::article) {
                
                include '../Views/Magic/Components/MagicParagraph.php';
            } else {
                $this->displayImageSection($section);
                
            }
        }
    }
    public function displayImageSection(PageSection $section): void
    {
        $imageSectionStyle = $this->imageSectionStyle($section);
        include '../Views/Magic/Components/MagicArticleWithImage.php';
    }
    public function displayGameSections($gameSections): void
    {
        foreach ($gameSections as $section) {
            include '../Views/Magic/Components/GameCard.php';
            
        }
    }

    /**
     * @return array{first:?PageSection, second:?PageSection}
     */
    public function getAdjacentPairByType(SectionType $firstType, SectionType $secondType): array
    {
        $sections = $this->page->content_sections;

        usort(
            $sections,
            fn(PageSection $a, PageSection $b) => ($a->display_order ?? 0) <=> ($b->display_order ?? 0)
        );

        $count = count($sections);
        for ($i = 0; $i < $count - 1; $i++) {
            $first = $sections[$i];
            $second = $sections[$i + 1];

            if ($first->section_type === $firstType && $second->section_type === $secondType) {
                return ['first' => $first, 'second' => $second];
            }
        }

        return ['first' => null, 'second' => null];
    }

    /**
     * @return array{image:?PageSection, article:?PageSection}
     */
    public function getClosingLorentzPair(): array
    {
        $pair = $this->getAdjacentPairByType(SectionType::image_bottom, SectionType::article);

        if ($pair['first'] !== null && $pair['second'] !== null) {
            return ['image' => $pair['first'], 'article' => $pair['second']];
        }

        $imageSection = null;
        $articleSection = null;

        foreach ($this->page->content_sections as $section) {
            $title = $section->title ?? '';
            $content = $section->content_html ?? '';

            if ($section->section_type === SectionType::image_bottom && str_contains($title, 'Lorentz')) {
                $imageSection = $section;
            }

            if ($section->section_type === SectionType::article && str_contains($content, 'Lorentz Formula')) {
                $articleSection = $section;
            }
        }

        return ['image' => $imageSection, 'article' => $articleSection];
    }
}