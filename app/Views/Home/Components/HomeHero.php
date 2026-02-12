<?php
namespace App\Views\Home\Components;
?>


<div class="w-[90%] mx-auto">
    <header class="mb-5 border-b-4 border-[--home-gold-accent] pb-2">
        <h1 class="text-6xl text-[var(--text-home-primary)] font-bold text-center mb-4">
            <?php echo htmlspecialchars($heroSection->title) ?></h1>
    </header>
    <div class="flex flex-row justify-evenly">
        <div class="w-[45%] ">
            <img src="/Assets/Home/HomeHero.png" alt="Haarlem Panorama"
                class="w-full h-auto object-cover rounded-l-3xl">
        </div>
        <div class="w-[45%] flex flex-col justify-center text-lg space-y-4">
            <?php echo $heroSection->content_html ?>
        </div>
    </div>
</div>