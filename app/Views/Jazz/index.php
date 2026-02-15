<?php
namespace App\Views\Jazz;

// Display any messages
echo isset($message) ? htmlspecialchars($message) : '';

// Use the organized sections passed from controller, fallback to manual extraction if needed
if (!isset($heroSection) || !isset($aboutSection)) {
    $heroSection = $heroSection ?? null;
    $aboutSection = $aboutSection ?? null;
    $artistSection = $artistSection ?? null;
    $scheduleSection = $scheduleSection ?? null;
    $venuesSection = $venuesSection ?? null;
    $ticketsSection = $ticketsSection ?? null;

    if (isset($sections) && is_array($sections)) {
        foreach ($sections as $section) {
            switch ($section->title) {
                case 'Where Jazz Meets History':
                    $heroSection = $heroSection ?? $section;
                    break;
                case 'About the Festival':
                    $aboutSection = $aboutSection ?? $section;
                    break;
                case 'Meet the Artists':
                    $artistSection = $artistSection ?? $section;
                    break;
                case 'Festival at a Glance':
                    $scheduleSection = $scheduleSection ?? $section;
                    break;
                case 'Venues':
                    $venuesSection = $venuesSection ?? $section;
                    break;
                case 'Tickets & Passes':
                    $ticketsSection = $ticketsSection ?? $section;
                    break;
            }
        }
    }
}
?>

<section class="flex flex-col gap-6 bg-gray-50 text-gray-900 pt-4">
    <!-- Hero Section -->
    <?php if (isset($heroSection)): ?>
        <?php include __DIR__ . '/Components/jazz-hero.php'; ?>
    <?php endif; ?>

    <!-- About Section -->
    <?php if (isset($aboutSection)): ?>
        <?php include __DIR__ . '/Components/jazz-about.php'; ?>
    <?php endif; ?>

    <!-- Artists Carousel Section -->
    <?php if (isset($artists) && !empty($artists)): ?>
        <?php include __DIR__ . '/Components/jazz-carousel.php'; ?>
    <?php endif; ?>

    <!-- Schedule Section -->
    <?php if (isset($scheduleSection)): ?>
        <?php include __DIR__ . '/Components/jazz-schedule.php'; ?>
    <?php endif; ?>

    <!-- Venues Section -->
    <?php if (isset($venuesSection)): ?>
        <?php include __DIR__ . '/Components/jazz-venues.php'; ?>
    <?php endif; ?>

    <!-- Tickets Section -->
    <?php if (isset($ticketsSection)): ?>
        <?php include __DIR__ . '/Components/jazz-tickets.php'; ?>
    <?php endif; ?>
</section>