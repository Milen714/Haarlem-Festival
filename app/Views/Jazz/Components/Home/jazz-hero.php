<?php

namespace App\Views\Jazz\Components\Home;

/**
 * Jazz Hero — full-width banner image with the festival introduction card.
 *
 * @var object|null $heroSection CMS section with title and optional media.
 */

$heroImagePath = '';
$heroImageAlt  = 'Jazz Festival Hero';

try {
    if (!empty($heroSection->media->file_path)) {
        $rawPath       = (string) $heroSection->media->file_path;
        $heroImagePath = str_starts_with($rawPath, '/') ? $rawPath : '/' . $rawPath;
        $heroImageAlt  = (string) ($heroSection->media->alt_text ?? $heroImageAlt);
    }
} catch (\Throwable $e) {
    $heroImagePath = '';
}
?>

<header class="relative w-full min-h-[300px] md:min-h-[584px]">

    <!-- Background: CMS image or gradient fallback -->
    <figure class="absolute inset-0 m-0">
        <?php if ($heroImagePath !== ''): ?>
            <img src="<?= htmlspecialchars($heroImagePath) ?>"
                 alt="<?= htmlspecialchars($heroImageAlt) ?>"
                 class="w-full h-full object-cover"/>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-[#6B2FD1] via-[#8B5CF6] to-[#A78BFA]"
                 role="img"
                 aria-label="Jazz Festival decorative background"></div>
        <?php endif; ?>
    </figure>

    <!-- Dark overlay to ensure text contrast -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent" aria-hidden="true"></div>

    <!-- Festival introduction card -->
    <div class="relative z-10 min-h-[300px] md:min-h-[584px] flex items-center px-4 md:px-8"
         style="max-width: 1400px; margin: 0 auto;">
        <section class="bg-white rounded-xl shadow-2xl w-full px-6 py-6 md:px-10 md:py-8 md:max-w-[580px]">
            <h1 class="font-bold mb-4 text-gray-900 text-3xl md:text-5xl"
                style="font-family: 'Cormorant Garamond', serif; line-height: 1.1;">
                <?= htmlspecialchars($heroSection->title ?? 'Where Jazz Meets History') ?>
            </h1>
            <p class="text-gray-700 leading-relaxed mb-3 text-base md:text-lg">
                <?= !empty($heroSection->content_html)
                    ? strip_tags($heroSection->content_html)
                    : 'Four days of live jazz across Haarlem\'s most iconic locations' ?>
            </p>
            <?php if (!empty($heroSection->content_html_2)): ?>
                <p class="text-gray-900 font-semibold text-base md:text-lg">
                    <?= strip_tags($heroSection->content_html_2) ?>
                </p>
            <?php endif; ?>
        </section>
    </div>

</header>
