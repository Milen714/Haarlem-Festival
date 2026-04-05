

<section class="bg-[#d4a356] py-20 px-6">
  <div class="text-center mb-12">
    <span class="text-[10px] uppercase font-bold tracking-widest border-b border-black pb-1">On the menu</span>
    <h2 class="text-3xl font-serif mt-4">Featured Dishes</h2>
    <p class="text-sm mt-2">Discover our signature creations, each crafted with precision.</p>
  </div>

  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($restaurant->gallery->media_items ?? [] as $media): ?>
        
          <figure class="bg-white/10 p-2 rounded shadow-lg overflow-hidden group">
             
                <img src="<?= htmlspecialchars($media->file_path) ?>" 
                alt="<?= htmlspecialchars($media->alt_text) ?>" class="w-full h-64 object-cover">
              
              <figcaption class="p-3 text-white font-serif italic text-lg text-center"><?= htmlspecialchars($media->alt_text) ?></figcaption>
          </figure>
       
      <?php endforeach; ?>
  </div>
</section>
