<?php
namespace App\ViewModels\Magic;
use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\SectionType;
use App\Models\Schedule;

class MagicTicketsViewModel{
    public array $availableDates;
    public array $schedulesByDate = [];
    
    public function __construct(array $schedulesByDate)
    {
        $this->schedulesByDate = $schedulesByDate;
    }

}