<?php /** @var App\CmsModels\PageSection $welcome */ ?>
<section class="container mx-auto max-w-[1100px] px-4 mt-12 mb-16">
    <?php if ($welcome): ?>
        <div class="text-center">
            <h2 class="font-serif text-2xl md:text-3xl text-ink-900">
                <?= htmlspecialchars($welcome->title) ?>
            </h2>
            <div class="underline-history mx-auto"></div>

            <div class="mt-4 max-w-3xl mx-auto text-ink-700 leading-relaxed italic prose prose-sm max-w-none">
                <?= $welcome->content_html ?>
            </div>
        </div>
    <?php endif; ?>
</section>