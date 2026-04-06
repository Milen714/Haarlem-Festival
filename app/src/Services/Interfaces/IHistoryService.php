<?php

namespace App\Services\Interfaces;

use App\ViewModels\History\TicketHistoryViewModel;
use App\CmsModels\PageSection;

interface IHistoryService
{
    public function getAvailableTourOptions(): TicketHistoryViewModel;
    public function getHomepageData(): array;
    public function getTourData(): array;
    public function getDetailData(string $slug): array;
    public function getTourRouteSection(): ?PageSection;
}