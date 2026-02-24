<?php /** @var App\CmsModels\PageSection $tourInfo */ ?>
<section class="rounded-xl bg-white shadow-md border border-[#e5e5e5] overflow-hidden">
    <?php if ($tourInfo): ?>
    <div class="flex items-center justify-between border-b border-[#e5e5e5] px-6 py-4">
        <h2 class="text-lg sm:text-xl font-semibold text-ink-900"><?= htmlspecialchars($tourInfo->title ?? 'The Tour') ?></h2>
    </div>

<div class="text-ink-700 leading-relaxed prose prose-sm max-w-none">
    <?= $tourInfo->content_html ?>
</div>
<?php endif; ?>
</section>