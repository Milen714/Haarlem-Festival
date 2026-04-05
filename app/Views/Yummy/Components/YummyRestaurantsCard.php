<?php 
namespace App\Views\Yummy\Components;
/**
 * @var \App\Models\Yummy\RestaurantListViewModel $viewModel;
 */

?>
<main>
  <section class="bg-[#d4a356] py-8 px-10" aria-labelledby="filter-heading">
    <h2 id="filter-heading" class="text-black font-bold mb-4 uppercase text-sm tracking-wider">Filter by Cuisine Type</h2>
    <a href="/events-yummy/restaurants" class="<?= !isset($_GET['cuisine']) ? 'bg-[#4a0e0e] text-white' : 'bg-white/90 text-black' ?>
      px-6 py-2 rounded shadow-md text-xs font-bold uppercase">All Cuisines
    </a>
    <?php foreach($viewModel->cuisines as $cuisine): ?>
      <a href="/events-yummy/restaurants?cuisine=<?= $cuisine->cuisine_Id ?>"
       class="<?= $viewModel->selectedCuisineId === $cuisine->cuisine_Id ?
       'bg-[#4a0e0e] text-white' : 'bg-white/90 text-black' ?>
       px-6 py-2 me-2 ms-1 rounded border border-black/10 text-xs font-bold uppercase hover:bg-white">
      <?= $cuisine->name ?>
      </a>
    <?php endforeach; ?>
  </section>

  <section class="bg-[#2d0a0a] py-16 px-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php foreach($viewModel->restaurants as $restaurant): ?>
      <article class="bg-white rounded-sm overflow-hidden flex flex-col shadow-2xl transition-transform hover:scale-[1.02]">
        <figure class="relative">
          <img src="<?= $restaurant->main_image->file_path ?? '' ?>" alt="<?= $restaurant->main_image->alt_text ?? $restaurant->name ?>" class="w-full h-48 object-cover">
          <div class="absolute bottom-2 left-2 flex gap-1">
            <span class="bg-[#d4a356] text-black text-[9px] font-black px-2 py-0.5 rounded">AvrPrc: <?= htmlspecialchars($restaurant->price_category) ?></span>
             <span class="bg-[#d4a356] text-black text-[9px] font-black px-2 py-0.5 rounded">Available Seats: <?= htmlspecialchars($restaurant->venue->capacity) ?></span>
             <span class="bg-[#d4a356] text-black text-[9px] font-black px-2 py-0.5 rounded">SESSION 3</span>
          </div>
        </figure>
        <div class="p-5 flex-grow">
          <h2 class="font-serif text-xl text-gray-900 mb-2"><?= $restaurant->name ?></h2>
          
          <div class="flex gap-2 mb-4">
            <?php foreach ($restaurant->cuisines as $cuisine): ?>
            <span class="text-[10px] px-2 py-0.5 border border-yellow-700 text-yellow-800 font-bold">
              <?= $cuisine['name'] ?>
            </span>
            <?php endforeach ?>
          </div>

          <address class="not-italic text-xs text-gray-500 mb-3 flex items-start">
            <span class="mr-1">📍</span> 
            <?= htmlspecialchars($restaurant->venue?->street_address ?? '') ?>
            <?= htmlspecialchars($restaurant->venue?->postal_code ?? '') ?>
            <?= htmlspecialchars($restaurant->venue?->city ?? '') ?>
          </address>

          <div class="flex items-center text-yellow-500 text-xs">
            <?= str_repeat('★', floor($restaurant->stars ?? 0)) ?>
            <span class="text-gray-400 ml-2 font-sans">
              (<?= $restaurant->review_count ?? 0 ?>)
            </span>
          </div>
        </div>
        <a href="/events-yummy/restaurants/<?= $restaurant->restaurant_id ?>" class="btn text-center bg-[#b38b4d] text-white py-3 text-sm font-black uppercase tracking-widest hover:bg-[#9a763f] transition-colors">
          View Details
        </a>
      </article>
    <?php endforeach ?>
      </div>
  </section>
</main>