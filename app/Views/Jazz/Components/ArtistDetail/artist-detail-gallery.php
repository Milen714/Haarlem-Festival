<?php

namespace App\Views\Jazz\Components\ArtistDetail;

?>

<section aria-labelledby="gallery-heading">
    <h2 id="gallery-heading" class="text-2xl md:text-3xl font-bold mb-6" style="font-family: 'Cormorant Garamond', serif;">
        Gallery
    </h2>

    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($vm->gallery->media_items as $mediaItem): ?>
            <?php
            if (empty($mediaItem->media?->file_path)) {
                continue;
            }

            try {
                $rawPath    = (string) $mediaItem->media->file_path;
                $imagePath  = str_starts_with($rawPath, '/') ? $rawPath : '/' . $rawPath;
                $imageAlt   = (string) ($mediaItem->media->alt_text ?? ($artist->name . ' photo'));
            } catch (\Throwable $e) {
                continue;
            }
            ?>
            <li class="rounded-xl overflow-hidden shadow hover:shadow-lg transition-shadow">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($imageAlt) ?>"
                    class="w-full h-44 md:h-56 object-cover" loading="lazy" />
            </li>
        <?php endforeach; ?>
    </ul>
</section>
