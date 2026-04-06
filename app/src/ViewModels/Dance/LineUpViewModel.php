<?php
namespace App\ViewModels\Dance;

use App\ViewModels\Dance\BaseViewModel;

class LineupViewModel extends BaseViewModel 
{
    public $artists;
    public $headLinerSection;
    public $schedulesSection;
    public array $groupedSchedules = [];

    public function __construct($pageData, $artists, $headLinerSection, $schedulesSection) 
    {
        parent::__construct($pageData);
        $this->artists = $artists;
        $this->headLinerSection = $headLinerSection;
        $this->schedulesSection = $schedulesSection;

        foreach ($this->schedulesSection as $item) {
            $dateKey = $item->date->format('Y-m-d');
            $this->groupedSchedules[$dateKey][] = $item;
        }

        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($pageData->title ?? 'Lineup');
    }
}