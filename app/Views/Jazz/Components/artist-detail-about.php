<?php
/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section aria-labelledby="bio-heading">
    <h2 id="bio-heading" class="text-3xl font-bold mb-4" style="font-family: 'Cormorant Garamond', serif;">
        About <?= htmlspecialchars($artist->name ?? '') ?>
    </h2>

    <p class="text-gray-700 leading-relaxed text-lg mb-6">
        <?= nl2br(htmlspecialchars($artist->bio)) ?>
    </p>
</section>