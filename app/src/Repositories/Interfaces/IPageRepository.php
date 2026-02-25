<?php
namespace App\Repositories\Interfaces;
use App\CmsModels\Page;
use App\CmsModels\PageSection;

interface IPageRepository
{
    public function getPageBySlug(string $slug): Page;
    public function updatePageSectionById(PageSection $section): bool;
    public function updatePage(Page $page): bool;
    public function getPageSlugs(): array;
}