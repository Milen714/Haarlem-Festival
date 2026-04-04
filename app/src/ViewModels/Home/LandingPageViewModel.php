<?php
namespace App\ViewModels\Home;

use App\CmsModels\Page;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\SectionType;
use App\ViewModels\Home\ScheduleList;
use App\ViewModels\Home\StartingPoints;

class LandingPageViewModel
{
    public Page $page;
    public ScheduleList $scheduleList;
    public StartingPoints $startingPoints;
    
    public ?PageSection $heroSection = null;
    
    /** @var PageSection[] $eventSections */
    public array $eventSections = [];
    
    public ?PageSection $eventTitleSection = null;
    
    public ?PageSection $scheduleSection = null;

    /**
     * @param Page $page The page data containing all sections
     * @param ScheduleList $scheduleList The organized schedule data
     * @param array $landmarks Array of landmark objects
     * @param array $venues Array of venue objects
     */
    public function __construct(Page $page, ScheduleList $scheduleList, array $landmarks, array $venues)
    {
        $this->page = $page;
        $this->scheduleList = $scheduleList;
        $this->startingPoints = new StartingPoints($landmarks, $venues);
        
        $this->organizeSections();
    }

    /**
     * Organizes all page sections into their appropriate categories.
     * Separates hero, event, schedule, and title sections for easy template access.
     */
    private function organizeSections(): void
    {
        $this->heroSection = PageSection::findHeroSection($this->page->content_sections);
        
        foreach ($this->page->content_sections as $section) {
            $title = $section->title ?? '';
            
            // Categorize event sections
            if ($section->section_type === SectionType::event_left || $section->section_type === SectionType::event_right) {
                $this->eventSections[] = $section;
            }
            
            // Find event title section
            if (str_contains($title, 'EventsSection')) {
                $this->eventTitleSection = $section;
            }
            
            // Find schedule section
            if (str_contains($title, 'ScheduleSection')) {
                $this->scheduleSection = $section;
            }
        }
    }

    /**
     * Check if hero section exists and should be displayed
     */
    public function hasHeroSection(): bool
    {
        return $this->heroSection !== null;
    }

    /**
     * Check if event sections exist
     */
    public function hasEventSections(): bool
    {
        return !empty($this->eventSections) && $this->eventTitleSection !== null;
    }

    /**
     * Check if schedule section exists
     */
    public function hasScheduleSection(): bool
    {
        return $this->scheduleSection !== null;
    }

    /**
     * Check if starting points (venues and landmarks) exist
     */
    public function hasStartingPoints(): bool
    {
        return !empty($this->startingPoints->startingPoints);
    }
}