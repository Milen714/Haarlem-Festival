<?php
namespace App\Views\Yummy\Components;
?>


<section class="bg-[var(--yummy-sec-section)] py-16 px-6 gap-6">
      <h2 class="text-xl font-bold text-gray-900 mb-8 text-center">
        More Events to See
      </h2>
      <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
        <div class="bg-white rounded shadow text-center">
          <img
            src="Assets/Jazz/JazzHome/Haarlem-Homepage.webp"
            class="rounded-t"
            alt="Jazz Festival"
          />
          <div class="p-4">
            <h3 class="font-semibold">Jazz - Haarlem Festival</h3>
            <a
            href="/events-jazz"
              class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)]  text-white text-sm px-4 py-2 rounded mt-3"
            >
              View more
            </a>
          </div>
        </div>
        <div class="bg-white rounded shadow text-center">
          <img
            src="Assets/Magic/AltHeroImage.png"
            class="rounded-t"
            alt="Magic Festival"
          />
          <div class="p-4">
            <h3 class="font-semibold">Magic - Haarlem Festival</h3>
            <a
              href="/events-magic"
              class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)]  text-white text-sm px-4 py-2 rounded mt-3"
            >
              View more
            </a>
          </div>
        </div>
        <div class="bg-white rounded shadow text-center">
          <img
            src="Assets/History/History_Church_1.png"
            class="rounded-t"
            alt="History Festival"
          />
          <div class="p-4">
            <h3 class="font-semibold">History - Haarlem Festival</h3>
            <a
              href="/events-history"
              class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] text-white text-sm px-4 py-2 rounded mt-3"
            >
              View more
            </a>
          </div>
        </div>
        <div class="bg-white rounded shadow text-center">
          <img
            src="Assets/Dance/DanceHome/69a41a0ad1451_1772362250.webp"
            class="rounded-t"
            alt="Dance Festival"
          />
          <div class="p-4">
            <h3 class="font-semibold">Dance - Haarlem Festival</h3>
           <a
              href="/events-dance"
              class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] border border-[var(--yummy-sec-section)] text-white text-sm px-4 py-2 rounded mt-3"
            >
              View more
            </a>
          </div>
        </div>
      </div>
    </section>
