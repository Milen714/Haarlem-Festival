<?php
namespace App\Services;
use App\CmsModels\TheFestivalPage;
use App\CmsModels\Enums\TheFestivalPageType;
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
}