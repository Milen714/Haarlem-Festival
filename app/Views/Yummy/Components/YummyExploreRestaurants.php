<?php
namespace App\Views\Yummy\Components;
/**@var object|null $heroSection */
?>


<!-- Explore 7 Restaurants -->
<section class="bg-[var(--yummy-primary)] text-white py-16 text-center gap-6">
  <h2 class="text-2xl font-bold text-[var(--yummy-sec-section)] mb-6">
    <?= htmlspecialchars($heroSection->title ?? 'Explore 7 Exceptional Restaurants') ?>
  </h2>
  <?= htmlspecialchars($heroSection->content_html ?? ' Discover culinary excellence! 7 exceptional restaurants participate in the festival,
    offering unforgettable dining experiences.') ?>
  <a
    href="<?= htmlspecialchars($heroSection->cta_url ?? '#restaurants') ?>"
    class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] px-6 py-2 rounded font-semibold"
    ><?= htmlspecialchars($heroSection->cta_text ?? 'View Restaurants') ?>
  </a>
</section>