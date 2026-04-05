<?php
namespace App\Views\History;

use App\Models\History\Landmark;

/** @var Landmark $landmark */
/** @var string $introImage */
/** @var string $historyImage */
/** @var string $whyVisitImage */
$landmark = $landmark ?? null;
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)] min-h-screen">
    
    <div class="max-w-content py-4 px-4 md:px-0">
        <nav aria-label="Breadcrumb" class="text-sm text-ink-500 mt-6 mb-8 flex flex-wrap gap-2 items-center">
            <a href="/" class="hover:text-ink-700 transition-colors">Home</a>
            <span class="mx-2 text-neutral-400">›</span>
            <a href="/history" class="hover:text-ink-700 transition-colors">Haarlem History</a>
            <span class="mx-2 text-neutral-400">›</span>
            <span class="text-ink-700 font-medium"><?= htmlspecialchars($landmark->name ?? '') ?></span>
        </nav>
    </div>

    <div class="max-w-content pb-12 px-4 md:px-0">
        <div class="flex flex-col items-center text-center">
            <h1 class="text-3xl md:text-5xl text-[var(--history-dark-brown)] font-history-serif font-bold mb-2">
                <?= htmlspecialchars($landmark->name ?? '') ?>
            </h1>
        </div>
    </div>

    <div class="max-w-content pb-16 overflow-hidden px-4 md:px-0">
        
        <section class="mb-20 flex flex-col md:flex-row items-center gap-8 md:gap-x-16">
            <div class="w-full md:w-[55%] flex flex-col justify-center p-[2rem]">
                <?php if (!empty($landmark->intro_title)): ?>
                    <h2 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 text-center">
                        <?= htmlspecialchars($landmark->intro_title) ?>
                    </h2>
                    <div class="underline-history mx-auto"></div>
                <?php endif; ?>
                <div class="mt-[1rem] text-[0.875rem] md:text-base leading-relaxed text-ink-700 italic prose prose-sm max-w-none [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                    <?= $landmark->intro_content ?? '' ?> 
                </div>
            </div>
            <div class="w-full md:w-[45%]">
                <img src="<?= htmlspecialchars($introImage) ?>" 
                     alt="Introduction to <?= htmlspecialchars($landmark->name ?? '') ?>" 
                     class="w-full h-[450px] object-cover rounded-[0.5rem] shadow-md">
            </div>
        </section>

        <section class="mb-20 flex flex-col md:flex-row-reverse items-center gap-8 md:gap-x-16">
            <div class="w-full md:w-[55%] flex flex-col justify-center p-[2rem]">
                <h2 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 text-center">
                    <?= htmlspecialchars($landmark->why_visit_title ?? 'Why to visit?') ?>
                </h2>
                <div class="underline-history mx-auto"></div>

                <div class="green-bullet-content mt-[1rem] text-[0.875rem] md:text-base leading-relaxed text-ink-700 italic prose prose-sm max-w-none [&>ul>li]:mb-2">
                    <?= $landmark->why_visit_content ?? '' ?>
                </div>
            </div>
            <div class="w-full md:w-[45%]">
                <img src="<?= htmlspecialchars($whyVisitImage) ?>"
                     alt="Why visit <?= htmlspecialchars($landmark->name ?? '') ?>"
                     class="w-full h-[450px] object-cover rounded-[0.5rem] shadow-md">
            </div>
        </section>

        <section class="mb-24 flex flex-col md:flex-row items-center gap-8 md:gap-x-16">
            <div class="w-full md:w-[55%] flex flex-col justify-center p-[2rem]">
                <h2 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 text-center">
                    <?= htmlspecialchars($landmark->detail_history_title ?? 'History') ?>
                </h2>
                <div class="underline-history mx-auto"></div>

                <div class="mt-[1rem] text-[0.875rem] md:text-base leading-relaxed text-ink-700 italic prose prose-sm max-w-none [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                    <?= $landmark->detail_history_content ?? '' ?>
                </div>
            </div>
            <div class="w-full md:w-[45%] relative">
                <img src="<?= htmlspecialchars($historyImage) ?>"
                     alt="History of <?= htmlspecialchars($landmark->name ?? '') ?>"
                     class="w-full h-[450px] object-cover rounded-[0.5rem] shadow-md">
            </div>
        </section>

        <?php if (!empty($otherLandmarks)): ?>
        <section class="text-center py-10 mt-12">
            <h3 class="font-history-serif text-2xl md:text-3xl text-ink-900">Discover more stories</h3>
            <div class="underline-history mx-auto"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 mt-8 max-w-3xl mx-auto px-4">
                <?php foreach ($otherLandmarks as $other): ?>
                    <?php
                        $otherImage = '/Assets/Home/ImagePlaceholder.png';
                        if (!empty($other->gallery) && !empty($other->gallery->media_items)) {
                            $items = array_values($other->gallery->media_items);
                            if (isset($items[0]) && !empty($items[0]->media)) {
                                $otherImage = '/' . ltrim($items[0]->media->file_path, '/');
                            }
                        }
                    ?>
                    <div>
                        <a href="/history/detail/<?= htmlspecialchars($other->landmark_slug) ?>"
                           class="landmark-title-hover block mb-3">
                            <?= htmlspecialchars($other->name) ?>
                        </a>
                        <img src="<?= htmlspecialchars($otherImage) ?>"
                             alt="<?= htmlspecialchars($other->name) ?>"
                             class="w-full h-[220px] object-cover rounded-[0.5rem] shadow-md">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="text-center py-10 mt-12">
            <h3 class="text-2xl font-history-serif text-ink-900 mb-6">
                Did you like what you saw? There is even more waiting
            </h3>
            <a href="/history/tour" class="mt-6 inline-flex items-center justify-center rounded-md btn-history hover:btn-history px-8 py-3 text-base font-semibold shadow-md transition-all">
                Book your tour now
            </a>
        </section>

    </div>
</div>