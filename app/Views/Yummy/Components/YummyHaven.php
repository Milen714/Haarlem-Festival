<?php 
namespace App\Views\Yummy\Components;

?>

<section class="bg-[var(--yummy-primary)] text-white py-16 px-6 md:px-12 gap-6">
      <div class="container mx-auto grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h2 class="text-2xl font-bold text-[var(--yummy-section)] mb-4">
            Haven for foodies!
          </h2>
          <p class="text-gray-200 mb-6 leading-relaxed">
            Take a trip through the wonderful city of Haarlem, where you can enjoy some of the best local food in town. 
            The Yummy Haarlem festival brings you an unforgettable journey with each dish
            telling its own story, rooted in rich history and culture.
          </p>
          <p class="text-gray-200 mb-8 leading-relaxed">
            Dine in some of the city's most beautiful restaurants and experience culinary passion like never before.
          </p>
          <a
            href="#restaurants"
            class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] font-semibold px-5 py-2 rounded"
          >
            View Restaurants
          </a>
        </div>
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Food_in_Haarlem.jpg"
          alt="Meal in Haarlem"
          class="rounded-lg shadow-lg"
        />
      </div>
    </section>