<?php
namespace App\ViewModels\Dance;

class VenueViewModel extends BaseViewModel 
{
    public $venues;

    public function __construct($pageData, $venues) 
    {
        parent::__construct($pageData);
        $this->venues = $venues;

        $this->addBreadcrumb('Home', '/events-dance');
        $title = $pageData->title ?? 'Venues';
        $this->addBreadcrumb($title);
    }
}