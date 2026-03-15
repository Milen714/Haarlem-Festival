


<section class="bg-gradient-to-b from-[#1a0505] to-[#4d0d0d] py-16 px-6">
  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
    <div class="space-y-6">
      <span class="inline-block bg-[#d4a356] text-black text-[10px] font-bold px-3 py-1 rounded-full uppercase">Meet the Chef</span>
      <h2 class="text-3xl font-serif text-white"><?= htmlspecialchars($restaurant->chef_name) ?></h2>
      <div class="text-gray-300 text-sm space-y-4 leading-relaxed">
        <?= htmlspecialchars($restaurant->chef_bio_text) ?>
      </div>
    </div>
    <figure class="relative">
      <img src="<?= htmlspecialchars($restaurant->chef_img->file_path) ?>" alt="<?= htmlspecialchars($restaurant->chef_img->alt_text) ?>" class="w-full rounded shadow-2xl grayscale hover:grayscale-0 transition duration-500">
    </figure>
  </div>
</section>