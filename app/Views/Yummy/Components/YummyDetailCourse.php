<?php


?>


<section class="bg-gradient-to-b from-[#d4a356] to-[#4a0e0e] py-16 px-6">
  <div class="max-w-4xl mx-auto bg-[#c5964a] p-10 rounded-sm shadow-inner text-[#1a0505]">
    <h2 class="text-3xl font-serif mb-4">Course Details</h2>
    <p class="text-sm mb-8 leading-relaxed">Join us for an exclusive culinary experience during the Haarlem Festival. Our special menu features a surprise selection of exquisite dishes.</p>

    <h3 class="font-bold text-xs uppercase mb-4 tracking-tighter">Available Sessions:</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
      <?php foreach($restaurant->sessions as $session): ?>
        <button class="bg-white/50 p-4 rounded text-center hover:bg-white transition">
          <span class="block text-[10px] uppercase font-bold">
            <?= htmlspecialchars($session->icon_url) ?> <br>
            <?= htmlspecialchars($session->name) ?>
          </span>
          <span class="block text-sm">
            <?= $session->start_time?->format('H:i') ?> - <?= $session->end_time?->format('H:i') ?>
            </span>
        </button>
      <?php endforeach ?>
    </div>

    <h3 class="font-bold text-xs uppercase mb-4 tracking-tighter">Cuisine Types:</h3>
   
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
       <?php foreach ($restaurant->cuisines as $cuisine): ?>
          <button class="bg-white/50 mb-6 p-4 rounded text-center hover:bg-white transition">
              <span class="text-xl"><?= htmlspecialchars($cuisine['icon_url']) ?></span>
              <div>
                <p class="font-bold"><?= htmlspecialchars($cuisine['name']) ?></p><p class="opacity-70">
                  <?= htmlspecialchars($cuisine['description']) ?>
                </p>
              </div>
          </button>
        <?php endforeach; ?>
      </div>

    <a href="#" class="mt-12 bg-black text-white px-8 py-3 text-xs font-bold uppercase tracking-widest hover:bg-gray-900">
      Make a Reservation
    </a>
  </div>
</section>