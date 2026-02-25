<?php
namespace App\Views\Dance\Components;
/** @var object|null $gallerySection */
?>
<section class="bg-[#0d0d0d] py-10">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 px-2">
        <?php if ($gallerySection && isset($gallerySection->gallery->images)): ?>
            <?php foreach ($gallerySection->gallery->images as $img): ?>
                <div class="aspect-square overflow-hidden bg-gray-900">
                    <img src="<?= $img->file_path ?>" 
                         class="w-full h-full object-cover hover:scale-110 transition duration-1000" 
                         alt="<?= $img->alt_text ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>