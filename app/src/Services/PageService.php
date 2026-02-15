<?php
namespace App\Services;
use App\CmsModels\Page;
use App\CmsModels\Enums\PageType;
use App\CmsModels\PageSection;
use App\Repositories\Interfaces\IPageRepository;
use App\Services\Interfaces\IPageService;

class PageService implements IPageService
{
    private IPageRepository $pageRepository;

    public function __construct(IPageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function getPageData(PageType $type): Page
    {
        return $this->pageRepository->getPageData($type);
    }
    public function updatePageSectionById(PageSection $section): bool
    {
        return $this->pageRepository->updatePageSectionById($section);
    }
    public function updatePage(Page $page): bool
    {
        return $this->pageRepository->updatePage($page);
    }
}