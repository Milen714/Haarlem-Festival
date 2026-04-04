<?php
namespace App\Views\Yummy\Components;
 
?>


<section class="bg-[var(--yummy-sec-section)] py-16 px-6 gap-6">
    <h2 class="text-xl font-bold text-gray-900 mb-8 text-center">
        More Events to See
    </h2>
    <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
        <?php if (!empty($events)): ?>
            <?php foreach($events as $event): ?>
            <?php include 'NearByEventCard.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>