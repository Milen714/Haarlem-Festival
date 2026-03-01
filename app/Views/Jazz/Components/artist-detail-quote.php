<?php
/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section class="text-center py-6">
    <blockquote class="text-2xl italic text-gray-800 max-w-3xl mx-auto leading-relaxed">
        "<?= htmlspecialchars($artist->featured_quote) ?>"
    </blockquote>
</section>