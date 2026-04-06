<?php
namespace App\ViewModels\Dance;

class VenueViewModel extends BaseViewModel {
    public $venues;

    /**
     * Assembles all data needed to render the Dance venues page.
     * Calls the parent constructor with pageData to initialise the base breadcrumb,
     * stores the venues list, then appends a Dance homepage link and the page title
     * as a non-linked breadcrumb entry.
     *
     * @param object $pageData  The CMS page object, used by the parent for base breadcrumb setup.
     * @param mixed  $venues    The list of Venue objects to display on the page.
     */
    public function __construct($pageData, $venues) {
        parent::__construct($pageData);
        $this->venues = $venues;

        $this->addBreadcrumb('Home', '/events-dance');
        $title = $pageData->title ?? 'Venues';
        $this->addBreadcrumb($title);
    }
}
