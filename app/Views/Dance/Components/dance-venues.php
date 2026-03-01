<?php
namespace App\Views\Dance\Components;
/** @var object|null $venueSection */
?>
<section class="bg-[#0d0d0d] py-24 px-6 md:px-20 border-t border-white/5">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
        
        <div class="w-full lg:w-1/2 relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-[#f08a8a] to-[#eeb44f] rounded-lg blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
            <div class="relative rounded-lg overflow-hidden border border-white/10">
                <?php if ($venueSection->media): ?>
                    <img src="<?= $venueSection->media->file_path ?>" alt="Festival Map" class="w-full h-auto">
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full lg:w-1/2 text-left">
            <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-8 pb-2">
                <?= htmlspecialchars($venueSection->section_title ?? 'The City is Your Dancefloor') ?>
            </h2>
            
            <?php if ($venueSection?->content_html): ?>
            <div class="prose prose-invert text-white prose-p:text-gray-400 prose-p:leading-relaxed max-w-none mb-10">
                <?= $venueSection?->content_html ?>
            </div>
            <?php endif; ?>

            <?php if ($venueSection?->cta_url): ?>
                <a href="<?= $venueSection->cta_url ?>" class="inline-block bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-3 px-8 rounded-full transition-all uppercase tracking-widest text-xs">
                    <?= $venueSection->cta_text?>
                </a>
            <?php endif; ?>
        </div>
        
    </div>
</section>