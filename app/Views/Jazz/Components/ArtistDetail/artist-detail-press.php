<?php

namespace App\Views\Jazz\Components\ArtistDetail;

/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section aria-labelledby="press-heading">
    <h2 id="press-heading" class="text-2xl font-bold mb-3" style="font-family: 'Cormorant Garamond', serif;">
        Press &amp; Recognition
    </h2>
    <blockquote class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg px-6 py-4 italic text-gray-700 text-lg">
        &ldquo;<?= htmlspecialchars($artist->press_quote) ?>&rdquo;
    </blockquote>
</section>
