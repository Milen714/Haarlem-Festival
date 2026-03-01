<?php 
namespace App\Views\Yummy\Components;
/**@var object|null $heroSection */

?>

<section class="bg-[var(--yummy-primary)] text-white py-16 px-6 md:px-12 gap-6">
      <div class="container mx-auto grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h2 class="text-2xl font-bold text-[var(--yummy-section)] mb-4">
            <?= htmlspecialchars($heroSection->title ?? 'Haven for foodies!') ?>
          </h2>
          <?= htmlspecialchars($heroSection->content_html ?? '') ?>
          
          <?= htmlspecialchars($heroSection->content_html_2 ?? "Dine in some of the city's most beautiful restaurants and experience culinary passion like never before.") ?>
          <a
            href="<?= htmlspecialchars($heroSection->cta_url ?? '#restaurants') ?>"
            class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] font-semibold px-5 py-2 rounded"
          >
            <?= htmlspecialchars($heroSection->cta_text ?? 'View Restaurants') ?>
        </div>
        <img
          src="..<?= htmlspecialchars($heroSection->media->file_path ?? '/assets/images/hero.webp') ?>"
          alt="<?= htmlspecialchars($heroSection->media->imageAlt ?? 'Meal in Haarlem') ?>"
          class="rounded-lg shadow-lg"
        />
      </div>
    </section>