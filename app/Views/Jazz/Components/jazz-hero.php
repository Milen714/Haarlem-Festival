<?php
namespace App\Views\Jazz\Components;
/** @var object|null $heroSection */
?>

<header class="relative w-full" style="height: 584px;">
    <!-- Hero Image Background -->
    <figure class="absolute inset-0">
        <?php if (isset($heroSection->media) && $heroSection->media && !empty($heroSection->media->file_path)): ?>
            <?php 
                // Ensure path starts with /
                $imagePath = $heroSection->media->file_path;
                if (strpos($imagePath, '/') !== 0) {
                    $imagePath = '/' . $imagePath;
                }
            ?>
            <img src="<?= htmlspecialchars($imagePath) ?>" 
                 alt="<?= htmlspecialchars($heroSection->media->alt_text ?? 'Jazz Festival Hero') ?>" 
                 class="w-full h-full object-cover" />
        <?php else: ?>
            <!-- Fallback gradient background -->
            <div class="w-full h-full bg-gradient-to-br from-[#6B2FD1] via-[#8B5CF6] to-[#A78BFA]"></div>
        <?php endif; ?>
    </figure>
    
    <!-- Dark overlay for better text contrast -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent"></div>
    
    <!-- Hero Content - Left aligned, centered vertically -->
    <div class="relative z-10 h-full flex items-center" style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
        <section class="bg-white rounded-xl shadow-2xl" style="padding: 2.5rem 3rem; max-width: 580px;">
            <h1 class="font-bold mb-4 text-gray-900" style="font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; line-height: 1.1;">
                <?= htmlspecialchars($heroSection->title ?? 'Where Jazz Meets History') ?>
            </h1>
            <p class="text-gray-700 leading-relaxed mb-3" style="font-size: 1.125rem;">
                Four days of live jazz across Haarlem's most iconic locations
            </p>
            <p class="text-gray-900 font-semibold" style="font-size: 1.125rem;">
                4 Days • 19 Artists • Grote Markt & Patronaat
            </p>
        </section>
    </div>
</header>