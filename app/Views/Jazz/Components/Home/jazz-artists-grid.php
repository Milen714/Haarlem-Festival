<?php

namespace App\Views\Jazz\Components\Home;

/**
 * Jazz Artists Grid — paginated card grid for all festival artists.
 *
 * @var \App\Models\MusicEvent\Artist[] $artists        All artists for this festival.
 * @var array                           $scheduleByDate  Schedule grouped by date (used to colour cards by day).
 */

/* ── Constants ── */
const ARTISTS_PER_PAGE  = 12;
const DAY_COLOR_PALETTE = ['lavender', 'pink', 'yellow', 'coral'];

/* ── Build a colour map: artist_id → day colour based on first scheduled day ── */
$artistDayColorMap = [];
$dayColorIndex     = 0;

try {
    foreach (($scheduleByDate ?? []) as $dateKey => $daySchedules) {
        if ($dateKey === 'unknown') {
            continue;
        }

        $dayColor = DAY_COLOR_PALETTE[$dayColorIndex % count(DAY_COLOR_PALETTE)];

        foreach ($daySchedules as $schedule) {
            $artistId = (int) ($schedule->artist_id ?? 0);
            if ($artistId > 0 && !isset($artistDayColorMap[$artistId])) {
                $artistDayColorMap[$artistId] = $dayColor;
            }
        }

        $dayColorIndex++;
    }
} catch (\Throwable $e) {
    $artistDayColorMap = [];
}

/* ── Paginate artists into pages of ARTISTS_PER_PAGE ── */
$artistPages = !empty($artists) ? array_chunk($artists, ARTISTS_PER_PAGE) : [];
$totalPages  = count($artistPages);
?>

<section class="py-6 bg-gray-50" aria-labelledby="artists-heading">
    <div class="container mx-auto px-4 max-w-[1600px]">

        <header class="mb-8">
            <h2 id="artists-heading"
                class="text-3xl md:text-5xl font-bold mb-2"
                style="font-family: 'Cormorant Garamond', serif;">
                Meet the Artists
            </h2>
            <p class="text-gray-600 text-base md:text-lg">Great talent across 4 unforgettable days</p>
        </header>

        <?php if (!empty($artistPages)): ?>
            <div class="relative">

                <!-- Grid track — one visible page at a time -->
                <div class="overflow-hidden px-4 md:px-8 lg:px-16">
                    <?php foreach ($artistPages as $pageIndex => $pageArtists): ?>
                        <div class="carousel-page"
                             data-page="<?= $pageIndex ?>"
                             <?= $pageIndex !== 0 ? 'hidden' : '' ?>>

                            <ol class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6 lg:gap-8"
                                aria-label="Artists page <?= $pageIndex + 1 ?>">
                                <?php foreach ($pageArtists as $artist):
                                    $artistId  = (int) ($artist->artist_id ?? 0);
                                    $cardColor = $artistDayColorMap[$artistId] ?? 'lavender';
                                ?>
                                    <li><?php include __DIR__ . '/jazz-artist-card.php'; ?></li>
                                <?php endforeach; ?>
                            </ol>

                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination arrows (only when more than one page) -->
                <?php if ($totalPages > 1): ?>
                    <button type="button"
                            data-action="carousel-prev"
                            class="absolute left-0 top-1/2 -translate-y-1/2 w-8 h-8 md:w-12 md:h-12 rounded-full bg-white border-2 border-gray-900
                                   flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all shadow-lg z-10"
                            aria-label="Previous artists page">
                        <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width:3;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <button type="button"
                            data-action="carousel-next"
                            class="absolute right-0 top-1/2 -translate-y-1/2 w-8 h-8 md:w-12 md:h-12 rounded-full bg-white border-2 border-gray-900
                                   flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all shadow-lg z-10"
                            aria-label="Next artists page">
                        <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width:3;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                <?php endif; ?>

            </div>

        <?php else: ?>
            <p class="text-center py-16 text-gray-600 text-lg">No artists available at this time.</p>
        <?php endif; ?>

    </div>
</section>

<script>
(function () {
    'use strict';

    const totalPages  = <?= $totalPages ?>;
    let activePage    = 0;

    function showPage(pageIndex) {
        const currentPageEl = document.querySelector('.carousel-page[data-page="' + activePage + '"]');
        const nextPageEl    = document.querySelector('.carousel-page[data-page="' + pageIndex + '"]');

        if (currentPageEl) currentPageEl.hidden = true;
        if (nextPageEl)    nextPageEl.hidden    = false;

        activePage = pageIndex;
    }

    function navigatePage(direction) {
        let nextIndex = activePage + direction;
        if (nextIndex < 0)           nextIndex = totalPages - 1;
        if (nextIndex >= totalPages) nextIndex = 0;
        showPage(nextIndex);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (event) {
            const target = event.target.closest('[data-action]');
            if (!target) return;

            if (target.dataset.action === 'carousel-prev') navigatePage(-1);
            if (target.dataset.action === 'carousel-next') navigatePage(1);
        });
    });
}());
</script>
