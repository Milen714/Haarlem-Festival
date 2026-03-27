<?php
namespace App\Views\Yummy\Components;

?>

<div id="confirmation-modal" class="fixed hidden inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
  
  <section class="relative w-full max-w-3xl bg-[#4a0e0e] border-2 border-[#d4a356] rounded-lg shadow-2xl p-8 md:p-12 text-center text-white font-sans">
    
    <button id="close-confirmation" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-[#d4a356] text-[#4a0e0e] hover:bg-[#c5964a] transition-colors" aria-label="Close modal">
      <span class="text-2xl font-bold">×</span>
    </button>

    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-[#d4a356] mb-6 shadow-inner">
        <span class="text-4xl" role="img" aria-label="Dining Icon">🍽️</span>
    </div>

    <hr class="border-[#d4a356]/40 w-3/4 mx-auto mb-8">

    <h2 class="text-3xl font-serif mb-2">Added to your Cart!</h2>
    <p class="text-gray-300 text-lg mb-10">Your reservation with <?= $restaurant->name ?> has been successfully staged for purchase.</p>

    <!-- <article class="relative flex items-center bg-white rounded-md overflow-hidden text-left mb-12 shadow-xl">
      <div class="w-2 self-stretch bg-red-600"></div>
      
      <div class="flex flex-col md:flex-row w-full p-4 items-center justify-between">
        <div class="bg-pink-100 p-3 rounded text-center min-w-[80px] mb-4 md:mb-0">
          <p id="confirm-day" class="text-[10px] font-bold uppercase text-gray-600">SAT</p>
          <p id="confirm-date" class="text-xs font-black text-gray-900">July 26</p>
        </div>

        <div class="flex-grow px-6">
          <span class="inline-block bg-gray-100 text-[9px] font-bold text-gray-600 px-2 py-0.5 rounded-full mb-1">HAARLEM YUMMY</span>
          <h3 class="text-gray-900 font-bold text-lg leading-tight"><?= $restaurant->name ?></h3>
          <p id="confirm-details" class="text-[11px] text-gray-500">
             2025-07-26 • 19:00-21:00 • Spaarne 96 <br>
             <span id="confirm-tickets" class="font-semibold italic text-gray-700">2x Adults, 2x Youths</span>
          </p>
        </div>

        <div class="flex flex-col items-end gap-2">
          <span id="confirm-total" class="text-2xl font-black text-gray-900">€ 175,00</span>
        </div>
      </div>
    </article> -->

    <hr class="border-[#d4a356]/40 w-3/4 mx-auto mb-10">

    <nav class="flex flex-col md:flex-row gap-4 justify-center" aria-label="Post-reservation actions">
      <button id="continue-shopping" class="bg-[#1a0505] border border-[#d4a356] text-[#d4a356] font-black uppercase text-sm tracking-widest px-10 py-4 rounded hover:bg-[#d4a356] hover:text-[#1a0505] transition-all">
        Continue Shopping
      </button>
      
      <button id="go-to-cart" class="bg-[#d4a356] text-[#1a0505] font-black uppercase text-sm tracking-widest px-10 py-4 rounded shadow-lg hover:bg-[#c5964a] transform hover:scale-105 transition-all">
        Go to Shopping Cart
      </button>
    </nav>

  </section>
</div>