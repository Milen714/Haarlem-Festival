


<section class="bg-[#d4a356] py-16">
  <div class="text-center mb-10">
    <h2 class="text-4xl font-serif">Visit Us</h2>
  </div>
  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 px-6">
    
    <div class="bg-white p-8 rounded shadow text-center">
      <div class="text-2xl mb-4">📍</div>
      <h4 class="font-bold mb-2">Location</h4>
      <p class="text-sm text-gray-600"><?= htmlspecialchars($restaurant->venue->street_address) ?>
      <br>
      <?= htmlspecialchars($restaurant->venue->postal_code) ?> ' '
      <?= htmlspecialchars($restaurant->venue->city) ?>
    </p>
    </div>
    
    <div class="bg-white p-8 rounded shadow text-center">
      <div class="text-2xl mb-4">📞</div>
      <h4 class="font-bold mb-2">Phone</h4>
      <p class="text-sm text-gray-600">
        <?= htmlspecialchars($restaurant->venue->phone) ?>
        <br>Mon-Sun: 11:00 - 22:00</p>
    </div>
    <div class="bg-white p-8 rounded shadow text-center">
      <div class="text-2xl mb-4">✉️</div>
      <h4 class="font-bold mb-2">Email</h4>
      <p class="text-sm text-gray-600"><?= htmlspecialchars($restaurant->venue->email) ?></p>
    </div>
  </div>
</section>