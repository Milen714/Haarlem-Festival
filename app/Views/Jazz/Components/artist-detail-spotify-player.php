<?php

$embedUrl = preg_replace(
    '#open\.spotify\.com/(artist|album|track)/#',
    'open.spotify.com/embed/$1/',
    $artist->spotify_url ?? ''
);
?>

<section aria-labelledby="listen-heading">
    <h2 id="listen-heading"
        class="text-3xl font-bold mb-6"
        style="font-family: 'Cormorant Garamond', serif;">
        Listen
    </h2>

    <iframe
        src="<?= htmlspecialchars($embedUrl) ?>"
        width="100%"
        height="352"
        frameborder="0"
        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
        loading="lazy"
        class="rounded-xl shadow-md"
        title="<?= htmlspecialchars($artist->name ?? '') ?> on Spotify">
    </iframe>
</section>