<?php
namespace App\Services;
use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\Repositories\PageRepository;
use App\Repositories\Interfaces\IPageRepository;
use App\Services\Interfaces\IPageService;
use App\Exceptions\ResourceNotFoundException;

class PageService implements IPageService
{
    private IPageRepository $pageRepository;

    public function __construct()
    {
        $this->pageRepository = new PageRepository();
    }

    public function getPageBySlug(string $slug): Page
    {
        $page = $this->pageRepository->getPageBySlug($slug);
        if (!$page) {
            throw new ResourceNotFoundException('Page not found.');
        }
        return $page;
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