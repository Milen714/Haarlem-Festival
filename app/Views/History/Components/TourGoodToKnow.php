<?php /** @var App\CmsModels\PageSection $goodToKnow */ ?>

<?php if ($goodToKnow): ?>
<div class="p-[2rem] md:p-[3rem] mt-10 mb-16">
    <h3 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900">
        <?= htmlspecialchars($goodToKnow->title ?? 'Good to know') ?>
    </h3>
    <div class="underline-history"></div>
    
    <div class="mt-[1rem] text-[0.875rem] md:text-base leading-relaxed text-ink-700 italic prose prose-sm max-w-none [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                
        <?= $goodToKnow->content_html ?>
        
    </div>
</div>
<?php endif; ?>