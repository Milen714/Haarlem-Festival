<?php /** @var array $otherLandmarks */ 
/** @var string $welcome */
?>

<?php if (!empty($otherLandmarks)): ?>
<section class="text-center py-10 mt-12">
    <h3 class="font-history-serif text-2xl md:text-3xl text-ink-900"><?= htmlspecialchars($welcome->title ?? '') ?></h3>
    <div class="underline-history mx-auto"></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 mt-8 max-w-3xl mx-auto px-4">
        <?php foreach ($otherLandmarks as $other): ?>
            <?php
                $otherImage = '/Assets/Home/ImagePlaceholder.png';
                if (!empty($other->gallery) && !empty($other->gallery->media_items)) {
                    $items = array_values($other->gallery->media_items);
                    if (isset($items[0]) && !empty($items[0]->media)) {
                        $otherImage = '/' . ltrim($items[0]->media->file_path, '/');
                    }
                }
            ?>
            <div>
                <a href="/history/detail/<?= htmlspecialchars($other->landmark_slug) ?>"
                   class="landmark-title-hover block mb-3">
                    <?= htmlspecialchars($other->name) ?>
                </a>
                <img src="<?= htmlspecialchars($otherImage) ?>"
                     alt="<?= htmlspecialchars($other->name) ?>"
                     class="w-full h-[220px] object-cover rounded-[0.5rem] shadow-md">
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
