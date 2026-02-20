<?php
namespace App\Views\Home;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\SectionType;
/** @var PageSection[] $sections */
$heroSection = PageSection::findHeroSection($pageData->content_sections);
$eventSections = [];
$eventTitleSection = null;
$scheduleSection = null;



foreach ($pageData->content_sections as $section) {
    if ($section->section_type === SectionType::event_left || $section->section_type === SectionType::event_right) {
        $eventSections[] = $section;
    }
    if (str_contains($section->title, 'EventsSection')) {
        $eventTitleSection = $section;
    }
    if (str_contains($section->title, 'ScheduleSection')) {
        $scheduleSection = $section; 
    }
}

?>

<section class="flex flex-col gap-6 bg_colors_home text_colors_home pt-4">
    <?php if ($heroSection):
        include 'Components/HomeHero.php'; ?>
    <?php endif; ?>

    <?php if($eventSections && $eventTitleSection): ?>
    <?php include 'Components/HomeEvents.php'; ?>
    <?php endif; ?>

    <?php if ($scheduleSection): ?>
    <?php
        include 'Components/HomeSchedule.php'; ?>
    <?php endif; ?>

    <?php include 'Components/HomeMap.php'; ?>
</section>