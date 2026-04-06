<?php /** @var App\CmsModels\PageSection $bookTour */ ?>
<section class="relative container mx-auto max-w-[1100px] px-4 my-20">

    <?php if ($bookTour): ?>
        <div class="text-center p-10 relative z-10">
            <div class="mt-3 max-w-2xl mx-auto text-ink-700 prose prose-sm max-w-none">
                <?= $bookTour->content_html ?>
            </div>
            <a href="<?= htmlspecialchars($bookTour->cta_url) ?>" 
               class="mt-6 inline-flex items-center justify-center rounded-md bg-brand-600 hover:bg-brand-700 text-white px-8 py-3 text-base font-semibold shadow-md transition-all">
                <?= htmlspecialchars($bookTour->cta_text ?? 'Book now') ?>
            </a>
        </div>
    <?php endif; ?>
</section>