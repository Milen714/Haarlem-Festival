<?php
namespace App\Views\Yummy\Components;
/**@var object|null $heroSection */
?>

<section class="relative bg-[var(--yummy-primary)] text-white">
    <div class="absolute inset-0">
        <img src="..<?=$heroSection->media->file_path ?? '../Assets/Yummy/Home/hero.webp' ?>"
            alt="<?= htmlspecialchars($heroSection->media->imageAlt ?? 'Hero image for Yummy Haarlem Festival') ?>"
            class="w-full h-full object-cover opacity-60" />
    </div>
    <div class="relative text-center py-24 px-6 bg-[var(--yummy-primary)]/80 backdrop-blur-sm">
        <h1 class="text-4xl font-semibold mb-4"><?= $heroSection->title ?? 'Yummy!' ?></h1>
        <?= $heroSection->content_html ?? 'Welcome to the Yummy Haarlem Festival, where culinary delights await you! Explore the vibrant food scene of Haarlem and indulge in a gastronomic adventure like no other.' ?>

        <a href="<?= htmlspecialchars($heroSection->cta_url ?? '#restaurants') ?>"
            class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] font-semibold px-6 py-3 rounded">
            <?= htmlspecialchars($heroSection->cta_text ?? 'View Restaurants') ?>
        </a>
    </div>
</section>