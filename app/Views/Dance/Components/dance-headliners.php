<?php
/** @var \app\ViewModels\Dance\LineupViewModel $vm */
?>
<section class="mb-24">
    <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-widest border-b-2 border-[#FFB8B8] mb-8 pb-2">
        <?= htmlspecialchars($vm->headLinerSection->title ?? 'Headliners') ?>
    </h2>
    
    <?php if ($vm->headLinerSection->content_html): ?>
    <div class="mb-12">
        <?= $vm->headLinerSection->content_html ?>
    </div>
    <?php endif; ?>
    
    <div class="flex flex-wrap justify-between gap-4 md:gap-8">
        <?php foreach ($vm->artists ?? [] as $artist): ?>
            <?php if (!$artist->special_event): ?>
            <div class="flex flex-col items-center group cursor-pointer">
                 <a href="/events-dance/artist/<?= $artist->slug ?>" class="flex flex-col items-center group cursor-pointer h-full">
                    <div class="w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden mb-4 ring-2 ring-transparent group-hover:ring-[var(--dance-tag-color-1)] transition-all duration-300">
                        <img src="<?= $artist->profile_image->file_path ?>" 
                            alt="<?= htmlspecialchars($artist->name) ?>" 
                            class="w-full h-full object-cover  transition-all duration-500">
                    </div>
                </a>
                
                <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-widest group-hover:text-white transition-colors">
                    <?= htmlspecialchars($artist->name) ?>
                </span>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>