<?php /** @var array $breadcrumbs */ ?>
<nav aria-label="Breadcrumb" class="text-sm mt-6 mb-8 flex flex-wrap gap-1 items-center"
     style="color: var(--history-light-brown);">
    <?php foreach ($breadcrumbs as $i => $crumb): ?>
        <?php if (!empty($crumb['url'])): ?>
            <a href="<?= htmlspecialchars($crumb['url']) ?>"
               style="color: var(--history-light-brown);"
               class="hover:opacity-70 transition-opacity">
                <?= htmlspecialchars($crumb['label']) ?>
            </a>
        <?php else: ?>
            <span style="color: var(--history-dark-brown);" class="font-medium">
                <?= htmlspecialchars($crumb['label']) ?>
            </span>
        <?php endif; ?>
        <?php if ($i < count($breadcrumbs) - 1): ?>
            <span class="mx-1" style="color: var(--history-light-brown);">›</span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
