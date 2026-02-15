<?php
namespace App\Views\Jazz\Components;

// Uses the passed $cardIndex instead of static variable
$colors = ['lavender', 'pink', 'coral', 'yellow'];
$color = $colors[$cardIndex % 4];

// Handles the image path
$imagePath = null;
$altText = $artist->name ?? '';

if (isset($artist->profile_image) && $artist->profile_image) {
    $imagePath = $artist->profile_image->file_path;
    $altText = $artist->profile_image->alt_text ?? $altText;
} elseif (isset($artist->file_path)) {
    $imagePath = $artist->file_path;
    $altText = $artist->alt_text ?? $altText;
}

// Ensures leading slash
if ($imagePath && strpos($imagePath, '/') !== 0) {
    $imagePath = '/' . $imagePath;
}
?>

<article class="jazz_event_border_<?= $color ?> rounded-xl overflow-visible bg-white hover:shadow-xl transition-all duration-300 flex flex-col">
    <!-- Artist Image -->
    <figure class="relative overflow-hidden bg-gray-100 rounded-t-lg w-full" style="aspect-ratio: 1/1;">
        <?php if ($imagePath): ?>
            <img src="<?= htmlspecialchars($imagePath) ?>" 
                 alt="<?= htmlspecialchars($altText) ?>" 
                 class="w-full h-full object-cover" />
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-[var(--pastel-<?= $color ?>)] to-white flex items-center justify-center">
                <span class="text-white text-5xl font-bold">
                    <?= strtoupper(substr($artist->name ?? '', 0, 1)) ?>
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
        
        <a href="/artist/<?= htmlspecialchars($artist->slug ?? '') ?>" 
           class="inline-flex items-center justify-center gap-1.5 px-5 py-2 rounded-full border-2 border-gray-900 bg-white text-gray-900 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm"
           style="margin-top: auto;">
            More Info 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="stroke-width: 3;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </section>
</article>