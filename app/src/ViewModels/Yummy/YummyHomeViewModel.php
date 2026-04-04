<?php
namespace App\ViewModels\Yummy;

use App\CmsModels\Page;

class YummyHomeViewModel
{
    public Page $pageData;
    public array $sections;
    public array $venues;
    public array $restaurants;
    public array $galleryItems;
    public string $title;
    public array $events;

    public function __construct(
        Page $pageData,
        array $sections,
        array $venues,
        array $restaurants,
        array $galleryItems,
        string $title = '',
        array $events = []

    ) {
        $this->pageData = $pageData;
        $this->sections = $sections;
        $this->venues = $venues;
        $this->restaurants = $restaurants;
        $this->galleryItems = $galleryItems;
        $this->title = $title ?: ($pageData->title ?? 'Yummy Event');
        $this->events = $events;
    }

    public function extractGalleryFromSections(): array {
        foreach ($this->sections as $section) {
            if (!empty($section->gallery->media_items)) {
                return $section->gallery->media_items;
            }
        }
        return [];
    }
}