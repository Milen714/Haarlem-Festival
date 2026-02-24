<?php /** @var App\CmsModels\PageSection $cta */ ?>
<div class="my-6">
    <svg viewBox="0 0 1440 60" class="h-10 w-full text-gold-400" aria-hidden="true">
        <path d="M0,30 C120,0 240,60 360,30 C480,0 600,60 720,30 C840,0 960,60 1080,30 C1200,0 1320,60 1440,30" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="8 12"/>
    </svg>

    <section class="mx-auto max-w-3xl text-center my-8">
        <?php if ($cta): ?>
            <div class="text-ink-700 prose prose-sm max-w-none mx-auto leading-relaxed">
                <?= $cta->content_html ?>
            </div>
            <a href="<?= htmlspecialchars($cta->cta_url ?? '/history') ?>" class="mt-6 inline-flex items-center rounded-md border border-brand-600 bg-brand-600 px-6 py-3 text-white font-semibold shadow-sm hover:bg-brand-700 transition-colors">
                <?= htmlspecialchars($cta->cta_text ?? 'Read our History') ?>
            </a>
        <?php else: ?>
             <p class="text-ink-700">Do you want to know more about the city before your visit?</p>
             <p class="text-ink-700">Check some of our incredible locations’ history.</p>
             <a href="/history" class="mt-6 inline-flex items-center rounded-md bg-brand-600 px-6 py-3 text-white font-semibold shadow-sm hover:bg-brand-700 transition-colors">Read our History</a>
        <?php endif; ?>
    </section>

    <svg viewBox="0 0 1440 60" class="h-10 w-full text-gold-400" aria-hidden="true">
        <path d="M0,30 C120,0 240,60 360,30 C480,0 600,60 720,30 C840,0 960,60 1080,30 C1200,0 1320,60 1440,30" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="8 12"/>
    </svg>
</div>