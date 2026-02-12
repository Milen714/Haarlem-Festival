<?php
namespace App\Services;
use App\CmsModels\TheFestivalPage;
use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalSection;
use App\Repositories\Interfaces\IHomePageRepository;
use App\Services\Interfaces\IHomePageService;

class HomePageService implements IHomePageService
{
    private IHomePageRepository $homePageRepository;

    public function __construct(IHomePageRepository $homePageRepository)
    {
        $this->homePageRepository = $homePageRepository;
    }

    public function getPageData(TheFestivalPageType $type): TheFestivalPage
    {
        return $this->homePageRepository->getPageData($type);
    }
    public function updatePageSectionById(TheFestivalSection $section): bool
    {
        return $this->homePageRepository->updatePageSectionById($section);
    }
    public function updatePage(TheFestivalPage $page): bool
    {
        return $this->homePageRepository->updatePage($page);
    }
}