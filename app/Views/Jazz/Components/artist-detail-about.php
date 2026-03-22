<?php
/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section aria-labelledby="bio-heading">
    <h2 id="bio-heading" class="text-3xl font-bold mb-4" style="font-family: 'Cormorant Garamond', serif;">
        About <?= htmlspecialchars($artist->name ?? '') ?>
    </h2>

    <div class="border-l-4 jazz_event_border_<?= $accentColor ?> pl-6 bg-gray-50 rounded-r-xl py-4 pr-4">
        <p class="text-gray-700 leading-relaxed text-lg">
            <?= nl2br(htmlspecialchars($artist->bio)) ?>
        </p>
    </div>
</section>