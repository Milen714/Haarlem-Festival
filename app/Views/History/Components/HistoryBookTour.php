<?php /** @var App\CmsModels\PageSection $bookTour */ ?>
<section class="relative my-20 overflow-hidden">

    <?php if ($bookTour): ?>
        <!-- Left wavy dashed line -->
        <svg class="absolute left-0 top-1/2 -translate-y-1/2 hidden md:block"
             style="width: clamp(220px, 38vw, 500px);"
             viewBox="0 0 320 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,38 C45,15 90,60 145,38 C190,18 235,58 285,38 C300,30 315,45 320,36"
                  stroke="var(--history-light-brown)" stroke-width="2" stroke-dasharray="10,7"
                  fill="none" stroke-linecap="round"/>
        </svg>
        <!-- Right wavy dashed line -->
        <svg class="absolute right-0 top-1/2 -translate-y-1/2 hidden md:block"
              style="width: clamp(220px, 38vw, 500px);"
             viewBox="0 0 320 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,36 C5,45 20,30 35,38 C90,58 135,18 180,38 C235,60 280,15 320,38"
                  stroke="var(--history-light-brown)" stroke-width="2" stroke-dasharray="10,7"
                  fill="none" stroke-linecap="round"/>
        </svg>

        <div class="container mx-auto max-w-[1100px] px-4">
            <div class="text-center p-10 relative z-10">
                <div class="mt-3 max-w-lg mx-auto history-emphasis font-bold prose-xl text-xl">
                    <?= $bookTour->content_html ?>
                </div>
                <a href="<?= htmlspecialchars($bookTour->cta_url) ?>"
                   class="mt-6 inline-flex items-center justify-center rounded-md btn-history hover:btn-history px-8 py-3 text-base font-semibold shadow-md transition-all">
                    <?= htmlspecialchars($bookTour->cta_text ?? 'Book now') ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>
