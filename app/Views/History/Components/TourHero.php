<?php
/** @var App\CmsModels\PageSection $hero */ // <--- Declaramos la variable
?>

<section class="relative w-full bg-black overflow-hidden">
    <img src="<?= htmlspecialchars($hero->media->file_path) ?>" 
         alt="Haarlem History Hero" 
         class="w-full h-auto block object-cover" />

    <div class="absolute inset-0 z-10" style="background-color: rgba(0, 0, 0, 0.4);"></div>

    <div class="absolute inset-0 z-20 flex flex-col items-center justify-center p-4">
        <h1 class="text-white text-center font-serif text-4xl md:text-5xl lg:text-6xl drop-shadow-md history-hero-title">
            <?= htmlspecialchars($hero->title) ?>
        </h1>
    </div>
</section>
