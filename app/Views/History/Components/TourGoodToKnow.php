<?php /** @var App\CmsModels\PageSection $goodToKnow */ ?>

<?php if ($goodToKnow): ?>
<div class="border-t border-[#e5e5e5] px-6 py-8">
    <h3 class="text-lg font-semibold text-ink-900">
        <?= htmlspecialchars($goodToKnow->title ?? 'Good to know') ?>
    </h3>
    
    <div class="mt-4 text-sm leading-relaxed text-ink-700 
                [&>ul]:list-disc [&>ul]:pl-5 [&>ul]:space-y-3 
                marker:text-brand-600 marker:text-xs">
                
        <?= $goodToKnow->content_html ?>
        
    </div>
</div>
<?php endif; ?>