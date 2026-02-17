<?php
namespace App\Views\Jazz;

?>

<?php foreach ($sections as $section): ?>
    <?php
    // Match by title since section_type is generic ("text", "hero_picture", etc.)
    $sectionTitle = strtolower(trim($section->title ?? ''));
    
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
        // TODO: Create jazz-schedule.php component when ready
    }
    
    // Venues Section
    elseif (strpos($sectionTitle, 'venue') !== false) {
        $venuesSection = $section;
        if (!empty($venues)) {
            include __DIR__ . '/Components/jazz-venues.php';
        }
    }
    
    // Tickets Section
    elseif (strpos($sectionTitle, 'ticket') !== false || strpos($sectionTitle, 'pass') !== false) {
        $ticketsSection = $section;
        include __DIR__ . '/Components/jazz-tickets.php';
    }
    
    // Gallery Section
    elseif (strpos($sectionTitle, 'gallery') !== false) {
        // TODO: Create jazz-gallery.php component if needed
    }
    
    // Generic content section (fallback)
    else {
        if (!empty($section->content_html)) {
            echo '<div class="container mx-auto px-4 py-8">';
            echo $section->content_html;
            echo '</div>';
        }
    }
    ?>
<?php endforeach; ?>