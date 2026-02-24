<?php /** @var App\CmsModels\PageSection[] $tourFeatures */ ?>
<div class="grid grid-cols-2 gap-4">
    <?php foreach ($tourFeatures as $feature): ?>
        <div class="rounded-lg border border-[#e5e5e5] bg-[#fdf8eb] p-4">
            <div class="flex items-center gap-3">
                <div class="inline-flex size-9 items-center justify-center rounded-md bg-white text-brand-600 shadow-sm">
                    <?= $feature->media->svg_code ?? 'Ícono por defecto' ?>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-500"><?= htmlspecialchars($feature->title) ?></div>
                    <div class="font-medium text-ink-900 leading-tight"><?= strip_tags($feature->content_html) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>