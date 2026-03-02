<?php

?>

<section aria-labelledby="gallery-heading">
    <h2 id="gallery-heading"
        class="text-3xl font-bold mb-6"
        style="font-family: 'Cormorant Garamond', serif;">
        Gallery
    </h2>

    <ul class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($vm->gallery->media_items as $item): ?>
        <?php if (empty($item->media?->file_path)) continue; ?>
        <?php
            $path = $item->media->file_path;
            $path = str_starts_with($path, '/') ? $path : '/' . $path;
            $alt  = $item->media->alt_text ?? ($artist->name . ' photo');
        ?>
        <li class="rounded-xl overflow-hidden shadow hover:shadow-lg transition-shadow">
            <figure>
                <img src="<?= htmlspecialchars($path) ?>"
                     alt="<?= htmlspecialchars($alt) ?>"
                     class="w-full h-56 object-cover"
                     loading="lazy" />
            </figure>
        </li>
        <?php endforeach; ?>
    </ul>
</section>