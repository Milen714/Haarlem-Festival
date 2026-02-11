<?php
namespace App\Repositories\Interfaces;
use App\CmsModels\TheFestivalPage;
use App\CmsModels\Enums\TheFestivalPageType;

interface IHomePageRepository
{
    public function getPageData(TheFestivalPageType $type): TheFestivalPage;
}