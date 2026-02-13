<?php
namespace App\Services;
use App\CmsModels\Page;
use App\CmsModels\Enums\PageType;
use App\CmsModels\PageSection;
use App\Repositories\Interfaces\IHomePageRepository;
use App\Services\Interfaces\IHomePageService;

class HomePageService implements IHomePageService
{
    private IHomePageRepository $homePageRepository;

    public function __construct(IHomePageRepository $homePageRepository)
    {
        $this->homePageRepository = $homePageRepository;
    }

    public function getPageData(PageType $type): Page
    {
        return $this->homePageRepository->getPageData($type);
    }
    public function updatePageSectionById(PageSection $section): bool
    {
        return $this->homePageRepository->updatePageSectionById($section);
    }
    public function updatePage(Page $page): bool
    {
        return $this->homePageRepository->updatePage($page);
    }
}