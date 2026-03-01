<?php
/** @var \App\Models\MusicEvent\Artist $artist */
?>

<section>
    <h2 class="text-2xl font-bold mb-4" style="font-family: 'Cormorant Garamond', serif;">
        Available on
    </h2>
    <div class="flex flex-wrap gap-4">

        <?php if (!empty($artist->spotify_url)): ?>
        <a href="<?= htmlspecialchars($artist->spotify_url) ?>"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 bg-[#1DB954] text-white font-bold px-6 py-3 rounded-full hover:opacity-90 transition-opacity">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
            </svg>
            Spotify
        </a>
        <?php endif; ?>

        <?php if (!empty($artist->youtube_url)): ?>
        <a href="<?= htmlspecialchars($artist->youtube_url) ?>"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 bg-[#FF0000] text-white font-bold px-6 py-3 rounded-full hover:opacity-90 transition-opacity">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
            YouTube
        </a>
        <?php endif; ?>

        <?php if (!empty($artist->soundcloud_url)): ?>
        <a href="<?= htmlspecialchars($artist->soundcloud_url) ?>"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 bg-[#FF5500] text-white font-bold px-6 py-3 rounded-full hover:opacity-90 transition-opacity">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M1.175 12.225c-.041 0-.079.01-.116.02v7.572c.037.01.075.018.116.018h.899V12.22h-.9zm2.156-3.818c-.484 0-.93.144-1.298.388v11.04h1.298V8.407zm2.214-.79c-.566 0-1.025.458-1.025 1.023v10.395h1.025V7.617zm2.193-.438c-.638 0-1.155.517-1.155 1.155v9.68h1.155V7.18zm2.236.163c-.714 0-1.295.578-1.295 1.296v9.196h1.295V8.475zm2.181-.662c-.783 0-1.419.637-1.419 1.42v8.402h1.419V7.813zm1.986 1.25c-.803 0-1.455.652-1.455 1.455v7.317h1.455V9.063zm2.105-.81c-.87 0-1.579.707-1.579 1.578v6.804h1.579V8.253zm2.015 1.178c-.935 0-1.695.76-1.695 1.696v5.708h1.695v-7.404zm2.145-.94c-1.007 0-1.823.816-1.823 1.823v5.521h1.823v-7.344z"/>
            </svg>
            SoundCloud
        </a>
        <?php endif; ?>

    </div>
</section>