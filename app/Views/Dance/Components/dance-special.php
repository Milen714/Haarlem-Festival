<?php
namespace App\Views\Dance\Components;
/** @var object|null $specialSection */
?>
<section class="bg-black py-20 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-6 pb-2">
            <?= htmlspecialchars($specialSection->section_title ?? 'Back2Back Specials') ?>
        </h2>
        
        <?php if ($specialSection?->content_html): ?>
        <div class="text-white max-w-3xl mx-auto mb-16 leading-relaxed text-lg md:text-xl">
            <?= $specialSection->content_html ?? '' ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="relative group rounded-lg overflow-hidden bg-[#121212] flex flex-col">
                <div class="absolute bg-[var(--dance-tag-color-1)] top-4 left-4 z-20 px-3 py-2 rounded-md font-bold flex flex-col items-center leading-tight text-black">
                    <span class="text-[10px] uppercase">Jul</span>
                    <span class="text-lg">24</span>
                </div>

                <div class="h-80 overflow-hidden relative">
                    <img src="/Assets/Dance/DanceSpecials/nickafrojack.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#121212] to-transparent opacity-80"></div>
                </div>

                <div class="p-6 text-left mt-auto">
                    <h4 class="text-white font-bold text-lg mb-4">Nicky Romero & Afrojack</h4>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto mb-1">📍 Lichtfabriek</p>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto">⏰ 20:00 - 02:00</p>
                    
                    <div class="mt-8 flex justify-between items-center">
                        <a href="#" class="text-[10px] text-gray-500 underline hover:text-white transition-colors">More Info ></a>
                        <button class="bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-2 px-4 rounded-md text-[10px] transition-all">
                            BUY TICKETS <br> <span class="opacity-70">€ 75.00</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative group rounded-lg overflow-hidden bg-[#121212] flex flex-col">
                <div class="absolute bg-[var(--dance-tag-color-1)] top-4 left-4 z-20 px-3 py-2 rounded-md font-bold flex flex-col items-center leading-tight text-black">
                    <span class="text-[10px] uppercase">Jul</span>
                    <span class="text-lg">25</span>
                </div>

                <div class="h-80 overflow-hidden relative">
                    <img src="/Assets/Dance/DanceSpecials/arminmartinhardwell.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#121212] to-transparent opacity-80"></div>
                </div>

                <div class="p-6 text-left mt-auto">
                    <h4 class="text-white font-bold text-lg mb-4">Hardwell, Martin Garrix & Armin van Buuren</h4>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto mb-1">📍 Caprera Openluchttheater</p>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto">⏰ 14:00 - 23:00</p>
                    
                    <div class="mt-8 flex justify-between items-center">
                        <a href="#" class="text-[10px] text-gray-500 underline hover:text-white transition-colors">More Info ></a>
                        <button class="bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-2 px-4 rounded-md text-[10px] transition-all">
                            BUY TICKETS <br> <span class="opacity-70">€ 110.00</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative group rounded-lg overflow-hidden bg-[#121212] flex flex-col">
                <div class="absolute bg-[var(--dance-tag-color-1)] top-4 left-4 z-20 px-3 py-2 rounded-md font-bold flex flex-col items-center leading-tight text-black">
                    <span class="text-[10px] uppercase">Jul</span>
                    <span class="text-lg">26</span>
                </div>

                <div class="h-80 overflow-hidden relative">
                    <img src="/Assets/Dance/DanceSpecials/afrojacktiestonick.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#121212] to-transparent opacity-80"></div>
                </div>

                <div class="p-6 text-left mt-auto">
                    <h4 class="text-white font-bold text-lg mb-4">Afrojack, Tiësto & Nicky Romero</h4>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto mb-1">📍 Caprera Openluchttheater</p>
                    <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto">⏰ 14:00 - 23:00</p>
                    
                    <div class="mt-8 flex justify-between items-center">
                        <a href="#" class="text-[10px] text-gray-500 underline hover:text-white transition-colors">More Info ></a>
                        <button class="bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-2 px-4 rounded-md text-[10px] transition-all">
                            BUY TICKETS <br> <span class="opacity-70">€ 110.00</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <?php if ($specialSection?->content_html_2): ?>
        <div class="text-white max-w-3xl mx-auto mt-16 leading-relaxed text-lg md:text-xl">
            <?= $specialSection?->content_html_2 ?? '' ?>
        </div>
        <?php endif; ?>
        <?php if ($specialSection?->cta_url): ?>
            <a href="<?= $specialSection->cta_url ?>" 
               class="inline-block mt-12 bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-4 px-12 rounded-full transition-all uppercase tracking-widest text-sm">
                <?= $specialSection->cta_text?>
            </a>
        <?php endif; ?>
    </div>
</section>