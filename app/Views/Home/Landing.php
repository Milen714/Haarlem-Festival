<?php
namespace App\Views\Home;
use App\ViewModels\Home\LandingPageViewModel;

/** @var LandingPageViewModel $pageViewModel */
$heroSection = $pageViewModel->heroSection;
$eventSections = $pageViewModel->eventSections;
$eventTitleSection = $pageViewModel->eventTitleSection;
$scheduleSection = $pageViewModel->scheduleSection;
$startingPoints = $pageViewModel->startingPoints;
$scheduleList = $pageViewModel->scheduleList;
?>

<section class="flex flex-col gap-6 bg_colors_home text_colors_home pt-4 overflow-x-hidden">
    <?php if ($heroSection):
        include 'Components/HomeHero.php'; ?>
    <?php endif; ?>

    <?php if($eventSections && $eventTitleSection): ?>
    <?php include 'Components/HomeEvents.php'; ?>
    <?php endif; ?>



    <!-- <div id="schedule_container">
    </div> -->

    <?php if ($scheduleSection): ?>
    <?php
        include 'Components/HomeSchedule.php'; ?>
    <?php endif; ?>

    <?php include 'Components/HomeMap.php'; ?>

    <div id="map_container"></div>
</section>


<script src="/Js/ScheduleDateButtons.js"></script>
<script>
const scheduleContainer = document.getElementById('schedule_container');
const scheduleFilterContainer = document.getElementById('schedule-filter-container');
const spinner = document.getElementById('spinner');

document.addEventListener('DOMContentLoaded', () => {
    loadSchedule();

    // ONE listener, works forever
    scheduleContainer.addEventListener('click', (e) => {
        const link = e.target.closest('.schedule-filter-link');
        if (!link) return;

        e.preventDefault();

        const eventFilter = link.dataset.event || '';
        const dateFilter = link.dataset.date || '';

        const newUrl =
            `/?event=${encodeURIComponent(eventFilter)}&date=${encodeURIComponent(dateFilter)}`;
        history.pushState({}, '', newUrl);

        loadSchedule();
    });
});

const loadSchedule = async () => {
    scheduleContainer.innerHTML = '';
    // Show spinner while loading
    spinner.classList.remove('hidden');
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

        if (window.displayDateButtons) {
            await window.displayDateButtons();
        }
    } catch (error) {
        console.error('Error fetching schedule:', error);
        scheduleContainer.innerHTML =
            '<p class="text-red-500">Failed to load schedule. Please try again later.</p>';
    } finally {
        spinner.classList.add('hidden');
        console.log('Schedule load attempt finished.');
    }
}

const loadMap = async () => {
    try {
        const response = await fetch('/starting-points');
        const html = await response.text();
        document.getElementById('map_container').innerHTML = html;
    } catch (error) {
        console.error('Error fetching map:', error);
        document.getElementById('map_container').innerHTML =
            '<p class="text-red-500">Failed to load map. Please try again later.</p>';
    }
}
</script>