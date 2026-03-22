<?php
/** @var \App\ViewModels\Dance\ArtistDetailViewModel $vm */
$items = $vm->breadcrumbs;

$trackId = null;
if (!empty($vm->artist) && !empty($vm->artist->spotify_url)) {
    $spotifyUrl = $vm->artist->spotify_url;
    preg_match('/track\/([a-zA-Z0-9]+)/', $spotifyUrl, $matches);
    if (isset($matches[1])) {
        $trackId = $matches[1];
    }
    }
?>

<div class="min-h-screen dance-bg text-white pb-24">
    <div class="max-w-7xl mx-auto px-6 pt-10 text-sm text-gray-400">
        <?php include __DIR__ . '/Components/breadcrumb.php'; ['items' => $items]; ?>
    </div>
    <header class="relative min-h-[70vh] w-full flex flex-col justify-start overflow-visible pb-16">
        <div class="absolute inset-0 z-0 h-screen overflow-hidden">
            <img src="<?= $vm->artist->profile_image->file_path ?>" 
                class="w-full h-full object-cover object-top opacity-60 grayscale" 
                alt="">
            
            <div class="absolute inset-0 bg-gradient-to-t from-[#0A0D16] via-[#0A0D16]/40 to-transparent"></div>
            
            <div class="absolute inset-0 bg-gradient-to-r from-[#0A0D16]/80 via-[#0A0D16]/20 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full pt-[45vh]">
            <div class="flex flex-col md:flex-row justify-between items-start gap-12">
                
                <div class="max-w-xl">
                    <h1 class="text-6xl md:text-8xl font-black uppercase tracking-tighter leading-none mb-6">
                        <?= htmlspecialchars($vm->artist->name) ?>
                    </h1>
                    
                    <?php if ($vm->artist->bio): ?>
                        <div id="artist-bio" class="leading-relaxed text-sm md:text-base line-clamp-4 transition-all duration-500 ease-in-out">
                            <?= nl2br(htmlspecialchars($vm->artist->bio)) ?>
                        </div>
                        <button id="read-more-btn" class="mt-8 uppercase tracking-[0.2em] transition">
                            Read more →
                        </button>
                    <?php else: ?>
                        <p class="text-gray-400">No biography available.</p>
                    <?php endif; ?>
                </div>

                <?php if ($trackId): ?>
                <div class="hidden md:block bg-[#1A1F2B]/90 backdrop-blur-md p-4 border border-gray-800 shadow-2xl">
                    <p class="text-[10px] uppercase tracking-[0.3em] text-gray-500 mb-4">Preview Track</p>
                    
                    <iframe 
                        style="border-radius:12px" 
                        src="https://open.spotify.com/embed/track/<?= $trackId ?>?utm_source=generator&theme=0" 
                        width="100%" 
                        height="152" 
                        frameBorder="0" 
                        allowfullscreen="" 
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
                        loading="lazy">
                    </iframe>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header> 
    <section class="max-w-5xl mx-auto px-6 mt-24">
        <h2 class="text-center text-2xl font-semibold mb-10 relative inline-block">
            Important tracks / albums
            <span class="block h-[2px] bg-[var(--dance-tag-color-1)] mt-2 mx-auto"></span>
        </h2>
        <?php if ($vm->albums): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mt-12">
                <?php foreach($vm->albums as $album): ?>
                    <div class="text-center group">
                        <img src="<?= $album->cover_image?->file_path ?? '/images/default-album.jpg' ?>"
                        class="w-full aspect-square object-cover transition duration-500"
                        alt="<?= htmlspecialchars($album->cover_image?->alt_text ?? $album->name) ?>">
                        <p class="mt-4">
                            <?= htmlspecialchars($album->name) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400">No albums available.</p>
        <?php endif; ?>
    </section>

    <section class="max-w-5xl mx-auto px-6 mt-24">
        <h2 class="text-center text-2xl font-semibold mb-10 relative inline-block">
            Upcoming events
            <span class="block h-[2px] bg-[var(--dance-tag-color-1)] mt-2 mx-auto"></span>
        </h2>
        <?php if ($vm->upcomingEvents): ?>
            <div class="space-y-12">
            <?php foreach ($vm->upcomingEvents as $dateKey => $slots): ?>
                <div class="border-b border-gray-800 pb-8 last:border-0">
                    <div class="flex flex-col gap-6">
                        <?php foreach ($slots as $session): ?>
                            <div class="flex justify-between items-center py-4">
                                
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold text-white mb-1">
                                        <?= date('l, d F Y', strtotime($dateKey)) ?> 
                                        <span class="mx-2 opacity-50">·</span> 
                                        <?= htmlspecialchars($session['venue_name']) ?>
                                    </h4>
                                    
                                    <p class="text-gray-400 text-sm font-medium tracking-wide">
                                        <?= htmlspecialchars($vm->artist->name) ?>
                                    </p>
                                    
                                    <p class="text-gray-500 text-xs mt-2 font-mono uppercase">
                                        <?= $session['start_time'] instanceof \DateTime ? $session['start_time']->format('H:i') : '--:--' ?> 
                                        - 
                                        <?= $session['end_time'] instanceof \DateTime ? $session['end_time']->format('H:i') : '--:--' ?>
                                    </p>
                                </div>

                                <div class="ml-8">
                                    <a href="/tickets/buy/<?= $session['schedule_id'] ?>"
                                    class="bg-[var(--dance-button-color)] hover:bg-white font-medium text-black px-6 py-3 rounded uppercase tracking-tighter transition-all duration-300 flex flex-col items-center justify-center">
                                        <span class="mb-1">BUY TICKETS</span>
                                        <span>€<?= number_format($session['price'] ?? 60, 2) ?></span>
                                    </a>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-400">No upcoming events available.</p>
        <?php endif; ?>
        </div>
    </section>  
    <section class="max-w-5xl mx-auto px-6 mt-24">

        <h2 class="text-center text-2xl font-semibold mb-10 relative inline-block">
            Gallery
            <span class="block h-[2px] bg-[var(--dance-tag-color-1)] mt-2 mx-auto"></span>
        </h2>

        <div class="space-y-4">
            <?php if ($vm->gallery): ?>
                <?php foreach($vm->gallery->media_items as $item): ?>
                    <img src="<?= htmlspecialchars($item->media->file_path) ?>"
                        class="w-full object-cover"
                        alt="<?= htmlspecialchars($item->media->alt_text) ?>">
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-center">No gallery images available.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bio = document.getElementById('artist-bio');
    const btn = document.getElementById('read-more-btn');

    if (btn && bio) {
        btn.addEventListener('click', function() {
            const isClamped = bio.classList.contains('line-clamp-4');
            
            if (isClamped) {
                bio.classList.remove('line-clamp-4');
                btn.textContent = 'Read less ↑';
            } else {
                bio.classList.add('line-clamp-4');
                btn.textContent = 'Read more →';
            }
        });
    }
});
</script>