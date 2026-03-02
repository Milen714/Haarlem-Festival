<?php
namespace App\Views\Yummy\Components;
/**
 * @var object|null $joinSection */
?>

<section class="bg-[var(--yummy-sec-section)] py-16 px-6 text-center gap-6">
      <h2 class="text-2xl text-[var(--yummy-primary)]  font-bold mb-6">
        <?= $joinSection->title ?? 'Join the Yummy Festival Experience' ?>
      </h2>
      <?= $joinSection->content_html ?? '' ?>
      <?= $joinSection->content_html_2 ?? '' ?>
      <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
        <div class="bg-white rounded-lg p-6 shadow">
          <h3 class="font-semibold">7 Restaurants</h3>
          <p class="text-sm text-gray-500 mt-2">Explore amazing choices in Haarlem</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow">
          <h3 class="font-semibold">4-Day Festival</h3>
          <p class="text-sm text-gray-500 mt-2">Multiple dining events and tastings</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow">
          <h3 class="font-semibold">All Over Haarlem</h3>
          <p class="text-sm text-gray-500 mt-2">Discover the beauty of the city</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow">
          <h3 class="font-semibold">Culinary Journey</h3>
          <p class="text-sm text-gray-500 mt-2">Taste local and international cuisines</p>
        </div>
      </div> -->
    </section>