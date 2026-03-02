<?php
namespace App\Views\Dance\Components;
/** @var object|null $heroSection */
?>
<section class="relative h-screen flex flex-col items-start justify-center overflow-hidden px-10 md:px-20">
    
    <?php if ($heroSection?->media): ?>
        <img src="<?= $heroSection->media->file_path ?>" 
             class="absolute inset-0 w-full h-full object-cover opacity-50" 
             alt="Hero Background">
    <?php endif; ?>
    
    <div class="relative">
        <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-[0.15em] mb-20">
            <?= htmlspecialchars($heroSection?->section_title ?? 'DANCE! presents: Back2Back') ?>
        </h1>
        
        <?php if ($heroSection?->content_html): ?>
        <article class="max-w-2xl mb-20">
            <div class="text-left text-lg md:text-xl text-gray-200 leading-relaxed">
                <?= $heroSection?->content_html ?? '' ?>
            </div>
        </article>
        <?php endif; ?>

        <?php if ($heroSection?->cta_url): ?>
            <a href="<?= $heroSection->cta_url ?>" 
               class="inline-block bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-4 px-12 rounded-full transition-all uppercase tracking-widest text-sm">
                <?= $heroSection->cta_text?>
            </a>
        <?php endif; ?>
    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20">
        <svg class="w-12 h-12 text-white animate-bounce opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
</section>