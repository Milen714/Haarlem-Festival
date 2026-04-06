<?php /** @var App\CmsModels\PageSection $tourInfo */ ?>
<section class="relative p-[2rem] md:p-[3rem] mb-10 flex flex-col justify-center">
    <?php if ($tourInfo): ?>
        <a href="/events-history" class="absolute -left-12 top-[2.3rem] md:top-[3.7rem] text-[var(--history-dark-brown)] hover:opacity-70 transition-opacity" aria-label="Back to Haarlem History">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="font-history-serif text-[1.75rem] md:text-[2.25rem] text-ink-900">
            <?= htmlspecialchars($tourInfo->title ?? 'The Tour') ?>
        </h2>
        <div class="underline-history"></div>

        <div class="text-[0.875rem] md:text-base text-ink-700 leading-relaxed italic prose prose-sm max-w-none">
            <?= $tourInfo->content_html ?>
        </div>
    <?php endif; ?>
</section>