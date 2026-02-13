<?php
namespace App\Repositories\Interfaces;
use App\CmsModels\Page;
use App\CmsModels\Enums\PageType;
use App\CmsModels\PageSection;

interface IPageRepository
{
    public function getPageData(PageType $type): Page;
    public function updatePageSectionById(PageSection $section): bool;
    public function updatePage(Page $page): bool;
}