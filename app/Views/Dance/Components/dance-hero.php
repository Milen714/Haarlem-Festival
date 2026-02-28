<?php
namespace App\Views\Dance\Components;
/** @var object|null $heroSection */
?>
<section class="relative h-screen flex flex-col items-center justify-center bg-black overflow-hidden px-6">
    <?php if ($heroSection?->media): ?>
        <img src="<?= $heroSection->media->file_path ?>" 
             class="absolute inset-0 w-full h-full object-cover opacity-40" 
             alt="Hero Background">
    <?php endif; ?>
    
    <div class="relative">
        <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-[0.15em] uppercase mb-8">
            <?= htmlspecialchars($heroSection?->section_title ?? 'DANCE! presents: Back2Back') ?>
        </h1>
        
        <div class="text-gray-200 text-left text-lg md:text-xl max-w-2xl mx-auto mb-10">
            <?= $heroSection?->content_html ?? 'Default fallback text if DB is empty' ?>
        </div>

        <?php if ($heroSection?->cta_url): ?>
            <a href="<?= $heroSection->cta_url ?>" 
               class="bg-[#F5C35E] text-white font-bold py-3 px-10 rounded-full transition-all uppercase tracking-widest text-xs">
                <?= $heroSection->cta_text ?? 'GET YOUR TICKETS' ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="absute bottom-30">
        <svg class="w-8 h-8 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
</section>