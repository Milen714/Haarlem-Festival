<?php

namespace App\Views\Jazz;

/**
 * Jazz Artist Detail page — hero + component sections for one artist.
 *
 * @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm       Artist detail view model.
 * @var object[]                                          $sections CMS page sections.
 */

/* ── CMS section display_order indices used on the artist detail page ── */
const CMS_SECTION_ARTIST_INTRO    = 1;
const CMS_SECTION_SCHEDULE_HEADER = 2;
const CMS_SECTION_TICKETS_CTA     = 3;

/* ── Accent colour: deterministic per artist slug so it is stable across page loads ── */
$accentColors  = ['lavender', 'pink', 'coral', 'yellow'];
$accentColor   = $accentColors[abs(crc32($vm->artist->slug ?? '')) % count($accentColors)];

/* ── Index CMS sections by display_order for easy lookup ── */
$cmsSections = [];
if (!empty($sections)) {
    foreach ($sections as $cmsSection) {
        $cmsSections[$cmsSection->display_order] = $cmsSection;
    }
}

$artist = $vm->artist;
?>

<?php include __DIR__ . '/Components/ArtistDetail/artist-detail-hero.php'; ?>

<main class="container mx-auto px-6 py-12 max-w-5xl space-y-8">

    <!-- CMS Section 1 — Featured artist intro text -->
    <?php if (!empty($cmsSections[CMS_SECTION_ARTIST_INTRO]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[CMS_SECTION_ARTIST_INTRO]->content_html ?>
        </div>
    <?php endif; ?>

    <!-- Bio -->
    <?php if (!empty($artist->bio)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-about.php'; ?>
    <?php endif; ?>

    <!-- Photo gallery -->
    <?php if ($vm->hasGallery()): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-gallery.php'; ?>
    <?php endif; ?>

    <!-- Career highlights & collaborations -->
    <?php if (!empty($artist->collaborations)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-collaborations.php'; ?>
    <?php endif; ?>

    <!-- Featured quote -->
    <?php if (!empty($artist->featured_quote)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-quote.php'; ?>
    <?php endif; ?>

    <!-- Press quote -->
    <?php if (!empty($artist->press_quote)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-press.php'; ?>
    <?php endif; ?>

    <!-- Discography -->
    <?php if (!empty($artist->albums)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-albums.php'; ?>
    <?php endif; ?>

    <!-- Spotify embed player -->
    <?php if (!empty($artist->spotify_url)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-listen.php'; ?>
    <?php endif; ?>

    <!-- Streaming & social links -->
    <?php if (!empty($artist->spotify_url) || !empty($artist->youtube_url) || !empty($artist->soundcloud_url)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-streaming-links.php'; ?>
    <?php endif; ?>

    <!-- CMS Section 2 — Performance schedule header -->
    <?php if (!empty($cmsSections[CMS_SECTION_SCHEDULE_HEADER]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[CMS_SECTION_SCHEDULE_HEADER]->content_html ?>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-schedule.php'; ?>

    <!-- Day / weekend pass upsell -->
    <?php if (!empty($passTicketTypes)): ?>
        <?php include __DIR__ . '/Components/ArtistDetail/artist-detail-passes.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/Components/Partials/purchase-overlay.php'; ?>
    <?php include __DIR__ . '/Components/Partials/purchase-overlay-js.php'; ?>

    <!-- CMS Section 3 — Tickets & Jazz CTA -->
    <?php if (!empty($cmsSections[CMS_SECTION_TICKETS_CTA]->content_html)): ?>
        <div class="cms-section">
            <?= $cmsSections[CMS_SECTION_TICKETS_CTA]->content_html ?>
            <?php if (!empty($cmsSections[CMS_SECTION_TICKETS_CTA]->cta_text) && !empty($cmsSections[CMS_SECTION_TICKETS_CTA]->cta_url)): ?>
                <div class="mt-4 text-center">
                    <a href="<?= htmlspecialchars($cmsSections[CMS_SECTION_TICKETS_CTA]->cta_url) ?>"
                       class="inline-block bg-black text-white px-8 py-3 rounded-full font-semibold hover:bg-gray-800 transition-colors">
                        <?= htmlspecialchars($cmsSections[CMS_SECTION_TICKETS_CTA]->cta_text) ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</main>
