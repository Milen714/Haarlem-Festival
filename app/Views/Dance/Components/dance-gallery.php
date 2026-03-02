<?php
namespace App\Views\Dance\Components;
/** @var object|null $gallerySection */
?>
<section class="bg-[#0d0d0d] py-20">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="inline-block text-[var(--dance-tag-color-1)] text-xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-12 pb-1">
            <?= htmlspecialchars($gallerySection?->title ?? 'Gallery') ?>
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-0 px-0">
            <?php if ($gallerySection && isset($gallerySection->gallery->media_items)): ?>
                <?php foreach ($gallerySection->gallery->media_items as $item): ?>
                    <?php 
                        // Extract the nested media object from the GalleryMedia item
                        $img = $item->media; 
                    ?>
                    <div class="aspect-square overflow-hidden bg-gray-900">
                        <img src="<?= $img->file_path ?>" 
                            class="w-full h-full object-cover hover:scale-110 transition duration-1000 cursor-pointer" 
                            alt="<?= htmlspecialchars($img->alt_text) ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 col-span-full py-10">No gallery images available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>