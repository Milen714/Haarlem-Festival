<?php
/** @var App\CmsModels\PageSection[] $landmarks */
$card1 = $landmarks[0] ?? null;
$card2 = $landmarks[1] ?? null;
$card3 = $landmarks[2] ?? null;
?>
<section class="space-y-[3rem] mb-[5rem]">
    <?php if ($card1): ?>
    <article class="grid md:grid-cols-2 gap-8 md:gap-x-16 items-stretch mb-5">
    <div class="md:col-span-1 order-2">
        <img src="<?= htmlspecialchars($card1->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" 
             class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md" />
    </div>
    
    <div class="md:col-span-1 order-1 p-[2rem] flex flex-col justify-center">
        <h4 class="font-serif text-[1.25rem] bold text-[var(--history-dark-brown)]"><?= htmlspecialchars($card1->title) ?></h4>
        <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
            <?= $card1->content_html ?>
        </div>
        <a href="<?= $card1->cta_url ?>" class="mt-[1.25rem] inline-block font-semibold text-[var(--history-accent-color)] hover:text-brand-700 transition-colors"><?= htmlspecialchars($card1->cta_text ?? 'Learn more') ?></a>
    </div>
</article>
    <?php endif; ?>

    <?php if ($card2): ?>
    <article class="grid md:grid-cols-2 gap-8 md:gap-x-16 items-stretch mb-5">
        <div class="md:col-span-1 order-1 md:order-1 p-[2rem] flex flex-col justify-center text-right md:text-left">
            <h4 class="font-serif text-[1.25rem] bold text-[var(--history-dark-brown)]"><?= htmlspecialchars($card2->title) ?></h4>
            <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
                <?= $card2->content_html ?>
            </div>
            <a href="<?= $card2->cta_url ?>" class="mt-[1.25rem] inline-block font-semibold text-[var(--history-accent-color)] hover:text-brand-700 transition-colors"><?= htmlspecialchars($card2->cta_text ?? 'Learn more') ?></a>
        </div>
        <div class="md:col-span-1 order-2 md:order-2">
            <img src="<?= htmlspecialchars($card2->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md" />
        </div>
    </article>
    <?php endif; ?>

    <?php if ($card3): ?>
    <article class="grid md:grid-cols-2 gap-8 md:gap-x-16 items-stretch mb-5">
        <div class="md:col-span-1 order-2">
            <img src="<?= htmlspecialchars($card3->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md" />
        </div>
        <div class="md:col-span-1 order-1 p-[2rem] flex flex-col justify-center">
            <h4 class="font-serif text-[1.25rem] bold text-[var(--history-dark-brown)]"><?= htmlspecialchars($card3->title) ?></h4>
            <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
                <?= $card3->content_html ?>
            </div>
            <a href="<?= $card3->cta_url ?>" class="mt-[1.25rem] inline-block font-semibold text-[var(--history-accent-color)] hover:text-brand-700 transition-colors"><?= htmlspecialchars($card3->cta_text ?? 'Learn more') ?></a>
        </div>
    </article>
    <?php endif; ?>
</section>