<?php
namespace App\Views\Dance\Components;
/** @var object|null $ticketSection */
?>
<section class="bg-black py-24 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-8 pb-2">
            <?= htmlspecialchars($ticketSection->section_title ?? 'All-Access Experience') ?>
        </h2>
        
        <?php if ($ticketSection?->content_html): ?>
        <div class="text-white text-lg max-w-4xl mx-auto mb-16 leading-relaxed md:text-base whitespace-pre-line">
            <?= $ticketSection->content_html ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-5xl mx-auto">
            
            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[var(--dance-tag-color-1)] transition-all duration-300 group relative">
                <span class="absolute -top-3 left-4 bg-red-600 text-white text-[10px] font-bold px-3 py-1 uppercase rounded-sm z-10">
                    Best Value
                </span>
                <h3 class="text-white text-xl font-bold mb-4 mt-4">Full Weekend</h3>
                <p class="text-[var(--dance-tag-color-1)] text-3xl font-black mb-6">€250.00</p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• Access to ALL days</li>
                    <li>• ALL B2B shows</li>
                    <li>• ALL Club events</li>
                </ul>
                <button class="w-full bg-[var(--dance-button-color)] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-colors mb-6">
                    BUY TICKETS
                </button>
                <p class="text-[10px] text-gray-600 leading-tight italic">
                    ℹ️ Entry subject to venue capacity regulations.
                </p>
            </div>

            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[var(--dance-tag-color-1)] transition-all duration-300 group">
                <h3 class="text-white text-xl font-bold mb-4 mt-4">Friday Access</h3>
                <p class="text-white text-3xl font-black mb-6">€125.00</p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• Nicky Romero & Afrojack B2B</li>
                    <li>• All Friday Club Shows</li>
                </ul>
                <button class="w-full bg-[var(--dance-button-color)] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-colors mb-6">
                    BUY TICKETS
                </button>
                <p class="text-[10px] text-gray-600 leading-tight italic">
                    ℹ️ Entry subject to venue capacity regulations.
                </p>
            </div>

            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[var(--dance-tag-color-1)] transition-all duration-300 group">
                <h3 class="text-white text-xl font-bold mb-4 mt-4">Saturday Access</h3>
                <p class="text-white text-3xl font-black mb-6">€150.00</p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• Hardwell, Garrix & Armin B2B</li>
                    <li>• All Saturday Club Shows</li>
                </ul>
                <button class="w-full bg-[var(--dance-button-color)] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-colors mb-6">
                    BUY TICKETS
                </button>
                <p class="text-[10px] text-gray-600 leading-tight italic">
                    ℹ️ Entry subject to venue capacity regulations.
                </p>
            </div>

            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[var(--dance-tag-color-1)] transition-all duration-300 group">
                <h3 class="text-white text-xl font-bold mb-4 mt-4">Sunday Access</h3>
                <p class="text-white text-3xl font-black mb-6">€150.00</p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• Afrojack, Tiësto & Romero B2B</li>
                    <li>• All Sunday Club Shows</li>
                </ul>
                <button class="w-full bg-[var(--dance-button-color)] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-colors mb-6">
                    BUY TICKETS
                </button>
                <p class="text-[10px] text-gray-600 leading-tight italic">
                    ℹ️ Entry subject to venue capacity regulations.
                </p>
            </div>

        </div>
    </div>
</section>