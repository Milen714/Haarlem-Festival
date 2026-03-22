<?php

namespace App\Views\Jazz\Components\Home;

// Color is mapped from the artist's schedule day in jazz-artists-grid.php.
$color = $cardColor ?? 'lavender';
?>

<article
    class="jazz_event_border_<?= $color ?> rounded-xl overflow-visible bg-white hover:shadow-xl transition-all duration-300 flex flex-col">
    <!-- Artist Image -->
    <figure class="relative overflow-hidden bg-gray-100 rounded-t-lg w-full" style="aspect-ratio: 1/1;">
        <?php if ($artist->hasProfileImage()): ?>
            <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>"
                alt="<?= htmlspecialchars($artist->getProfileImageAlt()) ?>" class="w-full h-full object-cover"
                loading="lazy" />
        <?php else: ?>
            <div
                class="w-full h-full bg-gradient-to-br from-[var(--pastel-<?= $color ?>)] to-white flex items-center justify-center">
                <span class="text-white text-3xl md:text-5xl font-bold">
                    <?= $artist->getInitial() ?>
                </span>
            </div>
        <?php endif; ?>
    </figure>

    <!-- Artist Info -->
    <footer class="p-3 md:p-4 text-center flex flex-col justify-between flex-grow">
        <h3 class="font-bold text-sm md:text-lg text-gray-900 mb-1 md:mb-2" style="line-height: 1.3;">
            <?= htmlspecialchars($artist->name ?? '') ?>
        </h3>
        <p class="text-xs text-gray-600 mb-2 md:mb-3" style="line-height: 1.4; min-height: 28px;">
            <?= htmlspecialchars($artist->genres ?? '') ?>
        </p>
        <a href="/events-jazz/artist/<?= htmlspecialchars($artist->slug ?? '') ?>"
            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 md:px-5 md:py-2 rounded-full border-2 border-gray-900 bg-white text-gray-900 hover:bg-gray-900 hover:text-white transition-all font-bold text-xs md:text-sm mt-auto"
            aria-label="View more information about <?= htmlspecialchars($artist->name ?? 'artist') ?>">
            More Info
            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                style="stroke-width: 3;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </footer>
</article>
