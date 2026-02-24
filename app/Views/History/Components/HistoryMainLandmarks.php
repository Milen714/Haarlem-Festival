<?php
/** @var App\CmsModels\PageSection[] $landmarks */
$card1 = $landmarks[0] ?? null;
$card2 = $landmarks[1] ?? null;
$card3 = $landmarks[2] ?? null;
?>
<section class="space-y-[3rem] mb-[5rem]">
    <?php if ($card1): ?>
    <article class="grid md:grid-cols-5 gap-[2rem] items-stretch">
        <div class="md:col-span-2 order-1">
            <img src="<?= htmlspecialchars($card1->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md border border-neutral-200" />
        </div>
        <div class="md:col-span-3 order-2 bg-[#fafafa] rounded-[0.5rem] border border-[#e5e5e5] p-[2rem] flex flex-col justify-center">
            <h4 class="font-serif text-[1.25rem] text-ink-900"><?= htmlspecialchars($card1->title) ?></h4>
            <div class="mt-[0.75rem] h-[1px] w-[3rem] bg-neutral-300"></div>
            <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
                <?= $card1->content_html ?>
            </div>
            <a href="/history/detail/<?= $card1->section_id ?>" class="mt-[1.25rem] inline-block font-semibold text-brand-600 hover:text-brand-700 transition-colors">Read more ...</a>
        </div>
    </article>
    <?php endif; ?>

    <?php if ($card2): ?>
    <article class="grid md:grid-cols-5 gap-[2rem] items-stretch">
        <div class="md:col-span-3 order-2 md:order-1 bg-[#fafafa] rounded-[0.5rem] border border-[#e5e5e5] p-[2rem] flex flex-col justify-center text-right md:text-left">
            <h4 class="font-serif text-[1.25rem] text-ink-900"><?= htmlspecialchars($card2->title) ?></h4>
            <div class="mt-[0.75rem] h-[1px] w-[3rem] bg-neutral-300 ml-auto md:ml-0"></div>
            <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
                <?= $card2->content_html ?>
            </div>
            <a href="/history/detail/<?= $card2->section_id ?>" class="mt-[1.25rem] inline-block font-semibold text-brand-600 hover:text-brand-700 transition-colors">Read more ...</a>
        </div>
        <div class="md:col-span-2 order-1 md:order-2">
            <img src="<?= htmlspecialchars($card2->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md border border-neutral-200" />
        </div>
    </article>
    <?php endif; ?>

    <?php if ($card3): ?>
    <article class="grid md:grid-cols-5 gap-[2rem] items-stretch">
        <div class="md:col-span-2 order-1">
            <img src="<?= htmlspecialchars($card3->media->file_path ?? '/Assets/Home/ImagePlaceholder.png') ?>" class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md border border-neutral-200" />
        </div>
        <div class="md:col-span-3 order-2 bg-[#fafafa] rounded-[0.5rem] border border-[#e5e5e5] p-[2rem] flex flex-col justify-center">
            <h4 class="font-serif text-[1.25rem] text-ink-900"><?= htmlspecialchars($card3->title) ?></h4>
            <div class="mt-[0.75rem] h-[1px] w-[3rem] bg-neutral-300"></div>
            <div class="mt-[1rem] text-[0.875rem] leading-relaxed text-ink-700 italic prose prose-sm max-w-none">
                <?= $card3->content_html ?>
            </div>
            <a href="/history/detail/<?= $card3->section_id ?>" class="mt-[1.25rem] inline-block font-semibold text-brand-600 hover:text-brand-700 transition-colors">Read more ...</a>
        </div>
    </article>
    <?php endif; ?>
</section>