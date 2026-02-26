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

    <div id="schedule_container">

    </div>
    <!-- <?php if ($scheduleSection): ?>
    <?php
        include 'Components/HomeSchedule.php'; ?>
    <?php endif; ?> -->

    <?php include 'Components/HomeMap.php'; ?>
</section>


<script>
const scheduleContainer = document.getElementById('schedule_container');
addEventListener('DOMContentLoaded', function() {
    attachScheduleFilterListeners();
    loadSchedule();
});

const attachScheduleFilterListeners = () => {
    // Use event delegation on the schedule container
    // This listener will work for all current and future filter links
    scheduleContainer.addEventListener('click', (e) => {
        const filterLink = e.target.closest('.schedule-filter-link');
        if (!filterLink) return;

        e.preventDefault();

        const eventFilter = filterLink.dataset.event || '';
        const dateFilter = filterLink.dataset.date || '';

        // Update URL without navigation using history.pushState
        const newUrl = `/?event=${encodeURIComponent(eventFilter)}&date=${encodeURIComponent(dateFilter)}`;
        window.history.pushState({}, '', newUrl);

        // Load schedule with new filters
        loadSchedule();
    });
};

const loadSchedule = async () => {
    scheduleContainer.innerHTML = '';
    try {
        // Get current filter parameters from URL
        const params = new URLSearchParams(window.location.search);
        const eventFilter = params.get('event') || '';
        const dateFilter = params.get('date') || '';

        // Build fetch URL with parameters
        const url =
            `/getSchedule?event=${encodeURIComponent(eventFilter)}&date=${encodeURIComponent(dateFilter)}`;

        const response = await fetch(url);
        const html = await response.text();
        scheduleContainer.innerHTML = html;
    } catch (error) {
        console.error('Error fetching schedule:', error);
        scheduleContainer.innerHTML =
            '<p class="text-red-500">Failed to load schedule. Please try again later.</p>';
    } finally {
        console.log('Schedule load attempt finished.');
    }
}
</script>