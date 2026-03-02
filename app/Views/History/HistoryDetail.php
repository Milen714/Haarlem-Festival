<?php
namespace App\Views\History;

use App\Models\History\Landmark;

/** @var Landmark $landmark */
$landmark = $landmark ?? null;

$introImage = '/Assets/Home/ImagePlaceholder.png';
$historyImage = '/Assets/Home/ImagePlaceholder.png';
$whyVisitImage = '/Assets/Home/ImagePlaceholder.png';

if (!empty($landmark->gallery) && !empty($landmark->gallery->media_items)) {
    if (isset($landmark->gallery->media_items[0]) && !empty($landmark->gallery->media_items[0]->media)) {
        $introImage = $landmark->gallery->media_items[0]->media->file_path;
    }

    if (isset($landmark->gallery->media_items[1]) && !empty($landmark->gallery->media_items[0]->media)) {
        $historyImage = $landmark->gallery->media_items[0]->media->file_path;
    }
    
    if (isset($landmark->gallery->media_items[2]) && !empty($landmark->gallery->media_items[1]->media)) {
        $whyVisitImage = $landmark->gallery->media_items[1]->media->file_path;
    }
}
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)] min-h-screen">
    
    <div class="max-w-content py-4">
        <nav class="text-sm font-semibold text-ink-500 flex flex-wrap gap-2 items-center">
            <a href="/" class="hover:text-brand-600 transition">Home</a>
            <span>&gt;</span>
            <a href="/history" class="hover:text-brand-600 transition">Haarlem History</a>
            <span>&gt;</span>
            <span class="text-ink-900"><?= htmlspecialchars($landmark->name ?? '') ?></span>
        </nav>
    </div>

    <div class="max-w-content pb-12">
        <div class="flex flex-col items-center text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-ink-900 font-history-serif mb-2">
                <?= htmlspecialchars($landmark->name ?? '') ?>
            </h1>
            <p class="text-xl md:text-2xl text-brand-600 font-semibold">
                <?= htmlspecialchars($landmark->short_description ?? '') ?>
            </p>
        </div>
    </div>

    <div class="max-w-content pb-16 overflow-hidden">
        
        <section class="mb-20 flex flex-col md:flex-row items-center gap-10">
            <div class="w-full md:w-[55%] flex flex-col justify-center">
                <?php if (!empty($landmark->intro_title)): ?>
                    <h2 class="text-3xl font-bold mb-4 text-ink-900 font-history-serif border-b-2 border-brand-600 pb-2 inline-block self-start">
                        <?= htmlspecialchars($landmark->intro_title) ?>
                    </h2>
                <?php endif; ?>
                <div class="text-lg text-ink-700 leading-relaxed space-y-4 [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                    <?= $landmark->intro_content ?? '' ?> 
                </div>
            </div>
            <div class="w-full md:w-[45%]">
                <img src="<?= htmlspecialchars($introImage) ?>" 
                     alt="Introduction to <?= htmlspecialchars($landmark->name ?? '') ?>" 
                     class="w-full h-[450px] object-cover rounded-xl shadow-md border border-[#e5e5e5]">
            </div>
        </section>

        <section class="mb-20 flex flex-col md:flex-row-reverse items-center gap-10">
            <div class="w-full md:w-[55%] flex flex-col justify-center">
                <h2 class="text-3xl font-bold mb-4 text-ink-900 font-history-serif border-b-2 border-brand-600 pb-2 inline-block self-start">
                    <?= htmlspecialchars($landmark->detail_history_title ?? 'History') ?>
                </h2>
                <div class="text-lg text-ink-700 leading-relaxed space-y-4 [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                    <?= $landmark->detail_history_content ?? '' ?> 
                </div>
            </div>
            <div class="w-full md:w-[45%] relative">
                <img src="<?= htmlspecialchars($historyImage) ?>" 
                     alt="History of <?= htmlspecialchars($landmark->name ?? '') ?>" 
                     class="w-full h-[450px] object-cover rounded-xl shadow-md border border-[#e5e5e5]">
                
                <?php if (!empty($landmark->gallery) && count($landmark->gallery->media_items) > 1): ?>
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
                    <?php foreach ($landmark->gallery->media_items as $index => $mediaItem): ?>
                        <span class="block w-2 h-2 rounded-full <?= $index === 0 ? 'bg-brand-600' : 'bg-white border border-[#e5e5e5]' ?> shadow-sm"></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="mb-24 flex flex-col md:flex-row items-center gap-10">
            <div class="w-full md:w-[55%] flex flex-col justify-center">
                <h2 class="text-3xl font-bold mb-4 text-ink-900 font-history-serif border-b-2 border-brand-600 pb-2 inline-block self-start">
                    <?= htmlspecialchars($landmark->why_visit_title ?? 'Why to visit?') ?>
                </h2>
                <div class="text-lg text-ink-700 leading-relaxed space-y-2 [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                    <?= $landmark->why_visit_content ?? '' ?> 
                </div>
            </div>
            <div class="w-full md:w-[45%]">
                 <img src="<?= htmlspecialchars($whyVisitImage) ?>" 
                      alt="Why visit <?= htmlspecialchars($landmark->name ?? '') ?>" 
                      class="w-full h-[450px] object-cover rounded-xl shadow-md border border-[#e5e5e5]">
            </div>
        </section>

       

        <section class="text-center py-10">
            <h3 class="text-2xl font-bold text-ink-900 mb-6 font-history-serif">
                Did you like what you saw? There is even more waiting
            </h3>
            <a href="/history/tour" class="home_history_button inline-block text-center min-w-[200px] text-lg">
                Book your tour now
            </a>
        </section>

    </div>
</div>