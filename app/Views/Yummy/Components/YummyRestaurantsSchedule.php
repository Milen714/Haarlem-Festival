<?php 
namespace App\Views\Yummy\Components;
/**
 * @var \App\Models\Yummy\RestaurantListViewModel $viewModel;
 */

?>


<section class="bg-gradient-to-b from-[#2d0a0a] to-[#c5964a] py-16 px-10">
  <div class="max-w-6xl mx-auto">
    <header class="flex items-center mb-10 text-white">
      <span class="text-3xl mr-4" role="img" aria-label="Calendar">📅</span>
      <h2 class="text-3xl font-serif">The Yummy Dining Schedule</h2>
    </header>

    <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-[#7a1b1b] text-white uppercase text-xs tracking-widest">
          <tr>
            <th class="p-5">Restaurant</th>
            <th class="p-5">Location</th>
            <th class="p-5">Session</th>
            <th class="p-5">Hours</th>
            <th class="p-5">Time</th>
            <th class="p-5">Near</th>
          </tr>
        </thead>
        <tbody class="text-sm text-gray-700 font-medium">
          <?php foreach($viewModel->restaurants as $restaurant): ?>
          <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
            <td class="p-5 font-bold text-[#7a1b1b]"> <?= $restaurant->name ?></td>
            <td class="p-5 text-gray-500">
              <?= htmlspecialchars($restaurant->venue->street_address) ?>
              , <?= htmlspecialchars($restaurant->venue->city) ?>
            </td>
            <td class="p-5">
              <span class="bg-[#7a1b1b] text-white px-3 py-1 rounded-full text-[10px]">Session 2</span>
            </td>
            <td class="p-5 font-bold">2.0</td>
            <td class="p-5">17:00 PM - 23:00 PM</td>
            <td class="p-5 text-gray-500">Pennings</td>
          </tr>
          <?php endforeach ?>
          </tbody>
      </table>
    </div>

    <aside class="mt-10 bg-white/95 p-8 rounded-lg border-l-8 border-[#7a1b1b] shadow-lg">
      <h4 class="font-black text-[#7a1b1b] uppercase tracking-tighter mb-2">Festival Information</h4>
      <p class="text-gray-700 leading-relaxed">
        All participating restaurants are within walking distance of major Haarlem landmarks. 
        Reservations are highly recommended during the festival period. Please note that session hours may vary!
      </p>
    </aside>
  </div>
</section>
