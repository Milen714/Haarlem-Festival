<?php

namespace App\Views\Jazz;

$sections = is_array($sections ?? null) ? $sections : [];
$jazzHomeVenues = is_array($venues ?? null) ? $venues : [];
$hasRenderedVenues = false;

?>

<?php foreach ($sections as $section): ?>
<?php
    // Restore canonical venue data on each iteration in case another component mutates $venues.
    $venues = $jazzHomeVenues;

    // Match by title since section_type is generic ("text", "hero_picture", etc.)
    $sectionTitle = strtolower(trim($section->title ?? ''));
    $isVenueSection =
        strpos($sectionTitle, 'venue') !== false ||
        strpos($sectionTitle, 'location') !== false ||
        strpos($sectionTitle, 'locatie') !== false ||
        strpos($sectionTitle, 'place') !== false;

    // Hero Section
    if (strpos($sectionTitle, 'history') !== false || strpos($sectionTitle, 'hero') !== false) {
        $heroSection = $section;
        include __DIR__ . '/Components/jazz-hero.php';
    }

    // About Section
    elseif (strpos($sectionTitle, 'about') !== false) {
        $aboutSection = $section;
        include __DIR__ . '/Components/jazz-about.php';
    }

    // Artists Section
    elseif (strpos($sectionTitle, 'artist') !== false) {
        $artistSection = $section;
        if (!empty($artists)) {
            include __DIR__ . '/Components/jazz-carousel.php';
        }
    }

    // Schedule Section
    elseif (strpos($sectionTitle, 'schedule') !== false || strpos($sectionTitle, 'glance') !== false) {
        $scheduleSection = $section;
        include __DIR__ . '/Components/jazz-schedule.php';
    }

    // Venues Section
    elseif ($isVenueSection) {
        $venuesSection = $section;
        $venues = $jazzHomeVenues;
        include __DIR__ . '/Components/jazz-venues.php';
        $hasRenderedVenues = true;
    }

    // Tickets Section
    elseif (strpos($sectionTitle, 'ticket') !== false || strpos($sectionTitle, 'pass') !== false) {
        $ticketsSection = $section;
        include __DIR__ . '/Components/jazz-tickets.php';
    }


    // Generic content section (fallback)
    else {
        $containsNavMarkup = stripos((string) ($section->content_html ?? ''), '<nav') !== false;
        if (!empty($section->content_html) && !$containsNavMarkup) {
            echo '<section class="container mx-auto px-4 py-8">';
            echo $section->content_html;
            echo '</section>';
        }
    }
?>
<?php endforeach; ?>

<?php if (!$hasRenderedVenues): ?>
<?php
    $venuesSection = (object) ['title' => 'Venues'];
    $venues = $jazzHomeVenues;
    include __DIR__ . '/Components/jazz-venues.php';
?>
<?php endif; ?>