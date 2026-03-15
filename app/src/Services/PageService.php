<?php
namespace App\Services;
use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\Repositories\PageRepository;
use App\Repositories\Interfaces\IPageRepository;
use App\Services\Interfaces\IPageService;

class PageService implements IPageService
{
    private IPageRepository $pageRepository;

    public function __construct()
    {
        $this->pageRepository = new PageRepository();
    }

    public function getPageBySlug(string $slug): Page
    {
        return $this->pageRepository->getPageBySlug($slug);
    }
    public function updatePageSectionById(PageSection $section): bool
    {
        return $this->pageRepository->updatePageSectionById($section);
    }
    public function updatePage(Page $page): bool
    {
        return $this->pageRepository->updatePage($page);
    }
    public function getPageSlugs(): array
    {
        return $this->pageRepository->getPageSlugs();
    }
}