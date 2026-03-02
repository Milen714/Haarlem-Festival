<?php
namespace App\Views\Yummy\Components;
/**
 * @var object|null $featuredCuisineSection
 * @var array|null $galleryItems
 */
?>

<section class="bg-[var(--yummy-sec-section)] text-[#FFFFFF] py-16 pt-4 pb-4 gap-6">
      <h2 class="text-center text-[var(--yummy-primary)] text-2xl font-bold mb-10">
        <?= $featuredCuisineSection->title ?? 'Featured Cuisines' ?>
      </h2>
      <div class="grid md:grid-cols-3 gap-6 max-w-6xl mx-auto px-6">
         <?php if (!empty($galleryItems)) : ?>
            
            <?php foreach ($galleryItems as $item) : ?>
                <div class="bg-gray-800 p-4 rounded-lg">
                    
                    <img
                        src="<?= htmlspecialchars($item->media->file_path ?? '/assets/images/default_cuisine.webp') ?>"
                        class="rounded mb-4"
                        alt="<?= htmlspecialchars($item->media->imageAlt ?? 'Featured Cuisine') ?>"
                    />

                    <h3 class="text-lg font-semibold">
                        <?= htmlspecialchars($item->media->title ?? 'Featured Cuisine') ?>
                    </h3>

                    <p class="text-sm text-gray-300">
                        <?= htmlspecialchars($item->media->alt_text ?? 'Featured cuisine description') ?>
                    </p>

                </div>
            <?php endforeach; ?>

        <?php else : ?>
            <p class="text-center col-span-3">No cuisines available.</p>
        <?php endif; ?>

      </div>
    </section>