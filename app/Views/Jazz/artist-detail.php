<?php

$artist      = $vm->artist;
$colors      = ['lavender', 'pink', 'coral', 'yellow'];
$accentColor = $colors[abs(crc32($artist->slug ?? '')) % 4];

// Index CMS page sections by display_order for easy lookup
$cmsSections = [];
if (!empty($sections)) {
    foreach ($sections as $s) {
        $cmsSections[$s->display_order] = $s;
    }
}
?>

<?php include __DIR__ . '/Components/artist-detail-hero.php'; ?>

<main class="container mx-auto px-6 py-12 max-w-5xl space-y-8">

    <?php /* CMS Section 1 – Featured Artist Intro */ ?>
    <?php if (!empty($cmsSections[1]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[1]->content_html ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($artist->bio)): ?>
        <?php include __DIR__ . '/Components/artist-detail-about.php'; ?>
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

    <?php if (!empty($artist->spotify_url)): ?>
        <?php include __DIR__ . '/Components/artist-detail-spotify-player.php'; ?>
    <?php endif; ?>

    <?php if (!empty($artist->spotify_url) || !empty($artist->youtube_url) || !empty($artist->soundcloud_url) || !empty($artist->website)): ?>
        <?php include __DIR__ . '/Components/artist-detail-links.php'; ?>
    <?php endif; ?>

    <?php /* CMS Section 2 – Performance Schedule Header */ ?>
    <?php if (!empty($cmsSections[2]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[2]->content_html ?>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/Components/artist-detail-schedule.php'; ?>

    <?php include __DIR__ . '/Components/jazz-ticket-modal.php'; ?>
    <?php include __DIR__ . '/Components/jazz-ticket-modal-js.php'; ?>

    <?php /* CMS Section 3 – Tickets & Jazz CTA */ ?>
    <?php if (!empty($cmsSections[3]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[3]->content_html ?>
            <?php if (!empty($cmsSections[3]->cta_text) && !empty($cmsSections[3]->cta_url)): ?>
                <div class="mt-4 text-center">
                    <a href="<?= htmlspecialchars($cmsSections[3]->cta_url) ?>"
                        class="inline-block bg-black text-white px-8 py-3 rounded-full font-semibold hover:bg-gray-800 transition-colors">
                        <?= htmlspecialchars($cmsSections[3]->cta_text) ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</main>