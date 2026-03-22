<?php

namespace App\Views\Jazz\Components\ArtistDetail;

/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section aria-labelledby="quote-heading" class="text-center py-6">
    <h2 id="quote-heading" class="sr-only">Featured Quote</h2>
    <blockquote class="text-2xl italic text-gray-800 max-w-3xl mx-auto leading-relaxed">
        &ldquo;<?= htmlspecialchars($artist->featured_quote) ?>&rdquo;
    </blockquote>
</section>
