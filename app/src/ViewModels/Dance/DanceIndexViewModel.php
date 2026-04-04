<?php
namespace App\ViewModels\Dance;

use App\ViewModels\Dance\BaseViewModel;


class DanceIndexViewModel extends BaseViewModel
{
    public $heroSection;
    public $artistSection;
    public $specialSection;
    public $venueSection;
    public $ticketSection;
    public $gallerySection;
    public string $title;

    public function __construct(
        ?object $pageData = null,
        ?object $heroSection = null,
        ?object $artistSection = null,
        ?object $specialSection = null,
        ?object $venueSection = null,
        ?object $ticketSection = null,
        ?object $gallerySection = null,
        string $title = 'Dance Event'
    ) {
        parent::__construct($pageData);
        
        $this->heroSection = $heroSection;
        $this->artistSection = $artistSection;
        $this->specialSection = $specialSection;
        $this->venueSection = $venueSection;
        $this->ticketSection = $ticketSection;
        $this->gallerySection = $gallerySection;
        $this->title = $title;

        $this->addBreadcrumb('Home', '/events-dance');
        $this->addBreadcrumb($title);
    }
}
