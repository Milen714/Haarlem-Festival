<?php
namespace App\Views\Jazz\Components;

// Color rotation for cards
$colors = ['lavender', 'pink', 'coral', 'yellow'];
$color = $colors[$cardIndex % 4];
?>

<article class="jazz_event_border_<?= $color ?> rounded-xl overflow-visible bg-white hover:shadow-xl transition-all duration-300 flex flex-col">
    <!-- Artist Image -->
    <figure class="relative overflow-hidden bg-gray-100 rounded-t-lg w-full" style="aspect-ratio: 1/1;">
        <?php if ($artist->hasProfileImage()): ?>
            <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>" 
                 alt="<?= htmlspecialchars($artist->getProfileImageAlt()) ?>" 
                 class="w-full h-full object-cover"
                 loading="lazy" />
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-[var(--pastel-<?= $color ?>)] to-white flex items-center justify-center">
                <span class="text-white text-5xl font-bold">
                    <?= $artist->getInitial() ?>
                </span>
            </div>
        <?php endif; ?>
    </figure>
    
    <!-- Artist Info -->
    <section class="p-4 text-center flex flex-col justify-between flex-grow">
        <div class="mb-3">
            <h3 class="font-bold text-base md:text-lg text-gray-900 mb-2" style="line-height: 1.3;">
                <?= htmlspecialchars($artist->name ?? '') ?>
            </h3>
            <p class="text-xs md:text-sm text-gray-600" style="line-height: 1.4; min-height: 32px;">
                <?= htmlspecialchars($artist->genres ?? '') ?>
            </p>
        </div>
        
        <a href="/events-jazz/artist/<?= htmlspecialchars($artist->slug ?? '')?>" 
           class="inline-flex items-center justify-center gap-1.5 px-5 py-2 rounded-full border-2 border-gray-900 bg-white text-gray-900 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm"
           style="margin-top: auto;"
           aria-label="View more information about <?= htmlspecialchars($artist->name ?? 'artist') ?>">
            More Info 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="stroke-width: 3;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </section>
</article>