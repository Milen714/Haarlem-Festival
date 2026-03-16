
<header class="w-full">
  <div class="h-64 md:h-96 w-full overflow-hidden">
    <img src="<?= htmlspecialchars($restaurant->banner_img) ?? htmlspecialchars($restaurant->main_image)  ?>" alt="Ratatouille Restaurant Exterior" class="w-full h-full object-cover">
  </div>
</header>

<main class="bg-[#1a0505] text-white">
  <section class="max-w-6xl mx-auto py-16 px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
    <div class="order-2 md:order-1">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-4xl font-serif text-[#d4a356]"><?= htmlspecialchars($restaurant->name) ?></h1>
        <div class="flex text-yellow-500 text-sm">
          <?= str_repeat('★', floor($restaurant->stars ?? 0)) ?>  
        <span class="ml-2 text-gray-400 font-sans">
          <?= htmlspecialchars($restaurant->stars ?? '0') ?>
        </span>
      </div>
      </div>
      <hr class="border-[#d4a356] mb-6 w-full">
      <p class="text-gray-300 leading-relaxed text-sm">
        <?= htmlspecialchars($restaurant->welcome_text) ?>
      </p>
    </div>
    <figure class="order-1 md:order-2 border-4 border-gray-800 rounded-sm">
      <img src="<?= htmlspecialchars($restaurant->main_image->file_path) ?>" alt="<?= htmlspecialchars($restaurant->main_image->alt_text ?? $restaurant->name) ?>" class="w-full shadow-2xl">
    </figure>
  </section>
</main>