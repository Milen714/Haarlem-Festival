<?php
namespace App\ViewModels\Dance;
use App\ViewModels\Dance\BaseViewModel;

class LineupViewModel extends BaseViewModel {
    public function __construct($pageData) {
        parent::__construct($pageData);
        
        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($pageData->title);
    }
}