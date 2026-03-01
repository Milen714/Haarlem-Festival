<?php
/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section>
    <h2 class="text-2xl font-bold mb-3" style="font-family: 'Cormorant Garamond', serif;">
        Press &amp; Recognition
    </h2>
    <blockquote class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg px-6 py-4 italic text-gray-700 text-lg">
        "<?= htmlspecialchars($artist->press_quote) ?>"
    </blockquote>
</section>