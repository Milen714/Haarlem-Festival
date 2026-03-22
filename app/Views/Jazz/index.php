<?php

namespace App\Views\Jazz;

/* ── Section title keywords used to detect which component to render ── */

const SECTION_KEYWORD_HERO     = ['history', 'hero'];
const SECTION_KEYWORD_ABOUT    = ['about'];
const SECTION_KEYWORD_ARTISTS  = ['artist'];
const SECTION_KEYWORD_SCHEDULE = ['schedule', 'glance'];
const SECTION_KEYWORD_VENUE    = ['venue', 'location', 'locatie', 'place'];
const SECTION_KEYWORD_TICKETS  = ['ticket', 'pass'];

/**
 * Check whether a section title contains any of the given keywords.
 *
 * @param string   $sectionTitle Lowercase-trimmed section title.
 * @param string[] $keywords     List of keywords to match against.
 */
function sectionTitleContains(string $sectionTitle, array $keywords): bool
{
    foreach ($keywords as $keyword) {
        if (strpos($sectionTitle, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

$sections       = is_array($sections ?? null) ? $sections : [];
$jazzHomeVenues = is_array($venues   ?? null) ? $venues   : [];
$venuesRendered = false;
?>

<?php foreach ($sections as $section): ?>
<?php
    /* Restore venue data on each iteration — a component must not mutate the canonical list. */
    $venues = $jazzHomeVenues;

    $sectionTitle = strtolower(trim((string) ($section->title ?? '')));

    if (sectionTitleContains($sectionTitle, SECTION_KEYWORD_HERO)) {
        $heroSection = $section;
        include __DIR__ . '/Components/Home/jazz-hero.php';
    } elseif (sectionTitleContains($sectionTitle, SECTION_KEYWORD_ABOUT)) {
        $aboutSection = $section;
        include __DIR__ . '/Components/Home/jazz-about.php';
    } elseif (sectionTitleContains($sectionTitle, SECTION_KEYWORD_ARTISTS)) {
        $artistSection = $section;
        if (!empty($artists)) {
            include __DIR__ . '/Components/Home/jazz-artists-grid.php';
        }
    } elseif (sectionTitleContains($sectionTitle, SECTION_KEYWORD_SCHEDULE)) {
        $scheduleSection = $section;
        include __DIR__ . '/Components/Home/jazz-schedule.php';
    } elseif (sectionTitleContains($sectionTitle, SECTION_KEYWORD_VENUE)) {
        $venuesSection  = $section;
        $venues         = $jazzHomeVenues;
        include __DIR__ . '/Components/Home/jazz-venues.php';
        $venuesRendered = true;
    } elseif (sectionTitleContains($sectionTitle, SECTION_KEYWORD_TICKETS)) {
        $ticketsSection = $section;
        include __DIR__ . '/Components/Home/jazz-tickets.php';
    } else {
        /* Generic fallback — render raw CMS HTML when it is not navigation markup */
        $containsNavMarkup = stripos((string) ($section->content_html ?? ''), '<nav') !== false;
        if (!empty($section->content_html) && !$containsNavMarkup) {
            echo '<section class="container mx-auto px-4 py-8">';
            echo $section->content_html;
            echo '</section>';
        }
    }
?>
<?php endforeach; ?>

<?php
/* Guarantee venues are always shown, even when no CMS section matched the venue keywords. */
if (!$venuesRendered):
    $venuesSection = (object) ['title' => 'Venues'];
    $venues        = $jazzHomeVenues;
    include __DIR__ . '/Components/Home/jazz-venues.php';
endif;
?>

<?php include __DIR__ . '/Components/Partials/purchase-overlay.php'; ?>
<?php include __DIR__ . '/Components/Partials/purchase-overlay-js.php'; ?>