<?php /** @var App\CmsModels\PageSection $tourInfo */ ?>
<section class="p-[2rem] md:p-[3rem] mb-10 flex flex-col justify-center">
    <?php if ($tourInfo): ?>
        <h2 class="font-history-serif text-[1.75rem] md:text-[2.25rem] text-ink-900">
            <?= htmlspecialchars($tourInfo->title ?? 'The Tour') ?>
        </h2>
        <div class="underline-history"></div>

        <div class="text-[0.875rem] md:text-base text-ink-700 leading-relaxed italic prose prose-sm max-w-none">
            <?= $tourInfo->content_html ?>
        </div>
    <?php endif; ?>
</section>