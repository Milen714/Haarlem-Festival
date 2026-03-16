<?php
/** @var array $items */
?>
<nav class="text-xs mb-10 uppercase tracking-[0.2em]">
    <div class="flex items-center text-white">
        <a href="javascript:history.back()" class="text-[var(--dance-tag-color-1)] hover:text-white transition-colors flex items-center mr-6">
            <span class="mr-2 text-lg leading-none mt-[-2px]">&lsaquo;</span> 
            Back
        </a>
        <?php foreach ($items as $index => $crumb): ?>
            <?php if (!empty($crumb['url'])): ?>
                <a href="<?= $crumb['url'] ?>" class="hover:text-[var(--dance-tag-color-1)] transition-colors">
                    <?= htmlspecialchars($crumb['label']) ?>
                </a>
            <?php else: ?>
                <span class="text-gray-300"><?= htmlspecialchars($crumb['label'] ?? '') ?></span>
            <?php endif; ?>

            <?php if ($index < count($items) - 1): ?>
                <span class="mx-3 text-gray-500">></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</nav>