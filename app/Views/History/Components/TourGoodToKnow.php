<?php /** @var App\CmsModels\PageSection[] $goodToKnowItems */ ?>
<div class="border-t border-[#e5e5e5] px-6 py-8">
    <h3 class="text-lg font-semibold text-ink-900">Good to know</h3>
    
    <ul class="mt-4 space-y-3 text-sm leading-relaxed text-ink-700">
        <?php foreach ($goodToKnowItems as $item): ?>
            <li class="flex items-start gap-3">
                <span class="mt-1 text-brand-600 text-[0.6rem]">●</span>
                
                <span><?= $item->content_html, '<b><strong><i><em>'?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>