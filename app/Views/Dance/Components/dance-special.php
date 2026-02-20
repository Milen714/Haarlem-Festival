<section class="bg-[#0d0d0d] py-20 px-6 border-t border-white/5">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="inline-block text-[#f08a8a] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[#f08a8a] mb-6 pb-2">
            <?= htmlspecialchars($specialSection->section_title ?? 'Back2Back Specials') ?>
        </h2>
        <div class="text-gray-400 max-w-3xl mx-auto mb-16">
            <?= $specialSection->content_html ?? '' ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-[#1a1a1a] p-1">
                <div class="h-64 bg-gray-800 mb-4 overflow-hidden">
                    <img src="/path/to/b2b-image.jpg" class="w-full h-full object-cover">
                </div>
                <div class="p-4 text-left">
                    <h4 class="text-white font-bold text-lg">Hardwell & Armin van Buuren</h4>
                    <p class="text-gray-500 text-sm mt-2 font-mono">Mainstage • 22:00 - 00:00</p>
                </div>
            </div>
        </div>
    </div>
</section>