<?php
namespace App\Services\Interfaces;
use App\CmsModels\TheFestivalPage;
use App\CmsModels\Enums\TheFestivalPageType;
interface IHomePageService
{
    public function getPageData(TheFestivalPageType $type): TheFestivalPage;
}