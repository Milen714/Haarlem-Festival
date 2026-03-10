<?php
namespace App\ViewModels\Dance;

use App\Models\Schedule;
use App\ViewModels\Dance\BaseViewModel;

class LineupViewModel extends BaseViewModel {
    public $artists;
    public $headLinerSection;
    public $schedulesSection;

    public function __construct($pageData, $artists, $headLinerSection, $schedulesSection) {
        parent::__construct($pageData);
        $this->artists = $artists;
        $this->headLinerSection = $headLinerSection;
        $this->schedulesSection = $schedulesSection;

        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($pageData->title);
    }
}