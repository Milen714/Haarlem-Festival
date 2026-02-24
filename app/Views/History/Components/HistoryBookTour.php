<?php /** @var App\CmsModels\PageSection $cta */ ?>
<section class="relative container mx-auto max-w-[1100px] px-4 my-20">
    
    <svg class="hidden lg:block absolute -left-16 top-1/2 -translate-y-1/2 w-40 h-20 text-gold-400 opacity-60" viewBox="0 0 200 100" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 10"><path d="M5,60 C50,5 120,95 195,40" /></svg>
    <svg class="hidden lg:block absolute -right-16 top-1/2 -translate-y-1/2 w-40 h-20 text-gold-400 opacity-60" viewBox="0 0 200 100" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="3 10"><path d="M5,40 C80,95 150,5 195,60" /></svg>

    <?php if ($cta): ?>
        <div class="text-center bg-white p-10 rounded-2xl border border-neutral-100 shadow-sm relative z-10">
            <h3 class="font-serif text-xl md:text-2xl text-ink-900">
                <?= htmlspecialchars($cta->title) ?>
            </h3>
            <div class="mt-3 max-w-2xl mx-auto text-ink-700 prose prose-sm max-w-none">
                <?= $cta->content_html ?>
            </div>
            <a href="<?= htmlspecialchars($cta->cta_url ?? '#') ?>" 
               class="mt-6 inline-flex items-center justify-center rounded-md bg-brand-600 hover:bg-brand-700 text-white px-8 py-3 text-base font-semibold shadow-md transition-all">
                <?= htmlspecialchars($cta->cta_text ?? 'Book now') ?>
            </a>
        </div>
    <?php endif; ?>
</section>