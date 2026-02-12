<?php
namespace App\Views\Yummy\Components;
?>

    <section class="relative bg-[var(--yummy-primary)] text-white">
      <div class="absolute inset-0">
        <img
          src="../Assets/Yummy/Home/Home_hero.png"
          alt="Outdoor dining in Haarlem"
          class="w-full h-full object-cover opacity-60"
        />
      </div>
      <div
        class="relative text-center py-24 px-6 bg-[var(--yummy-primary)]/80 backdrop-blur-sm"
      >
        <h1 class="text-4xl font-semibold mb-4">Yummy!</h1>
        <p class="max-w-2xl mx-auto text-lg mb-6">
          Experience the historical flavors of Haarlem with 7 amazing restaurants during a 4-day festival!
        </p>
        <a
          href="#restaurants"
          class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] font-semibold px-6 py-3 rounded"
        >
          View Restaurants
        </a>
      </div>
    </section>