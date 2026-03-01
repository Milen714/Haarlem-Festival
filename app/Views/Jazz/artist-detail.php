<?php

$artist      = $vm->artist;
$colors      = ['lavender', 'pink', 'coral', 'yellow'];
$accentColor = $colors[abs(crc32($artist->slug ?? '')) % 4];
?>

<?php include __DIR__ . '/Components/artist-detail-hero.php'; ?>

<main class="container mx-auto px-6 py-12 max-w-5xl space-y-16">

    <?php if (!empty($artist->bio)): ?>
        <?php include __DIR__ . '/Components/artist-detail-about.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->spotify_url)): ?>
        <?php include __DIR__ . '/Components/artist-detail-spotify-player.php'; ?>
    <?php endif; ?>

    <?php if ($vm->hasGallery()): ?>
        <?php include __DIR__ . '/Components/artist-detail-gallery.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->collaborations)): ?>
        <?php include __DIR__ . '/Components/artist-detail-collaborations.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->featured_quote)): ?>
        <?php include __DIR__ . '/Components/artist-detail-quote.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->press_quote)): ?>
        <?php include __DIR__ . '/Components/artist-detail-press.php'; ?>
    <?php endif; ?>

    

    <?php if (!empty($artist->albums)): ?>
        <?php include __DIR__ . '/Components/artist-detail-albums.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->spotify_url) || !empty($artist->youtube_url) || !empty($artist->soundcloud_url) || !empty($artist->website)): ?>
        <?php include __DIR__ . '/Components/artist-detail-links.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/Components/artist-detail-schedule.php'; ?>

    

</main>