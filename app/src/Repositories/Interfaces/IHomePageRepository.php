<?php
namespace App\Repositories\Interfaces;
use App\CmsModels\TheFestivalPage;
use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalSection;

interface IHomePageRepository
{
    public function getPageData(TheFestivalPageType $type): TheFestivalPage;
    public function updatePageSectionById(TheFestivalSection $section): bool;
    public function updatePage(TheFestivalPage $page): bool;
}