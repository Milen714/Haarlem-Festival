<?php
namespace App\Views\Jazz\Components;

/** @var \App\Models\MusicEvent\Artist[] $artists */

// Split artists into pages of 12 (6x2 grid)
$artistsPerPage = 12;
$pages = !empty($artists) ? array_chunk($artists, $artistsPerPage) : [];
$totalPages = count($pages);
?>

<section class="py-16 bg-gray-50" aria-labelledby="artists-heading">
    <div class="container mx-auto px-4 max-w-[1600px]">
        <header class="mb-12">
            <h2 id="artists-heading" class="text-4xl md:text-5xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Meet the Artist's
            </h2>
            <p class="text-gray-600 text-lg">Great talent across 4 unforgettable days</p>
        </header>
        
        <?php if (!empty($artists)): ?>
        <div class="relative">
            
            <!-- Carousel Track -->
            <div class="overflow-hidden px-16">
                <div id="carousel-track" class="transition-transform duration-700 ease-in-out">
                    
                    <?php foreach ($pages as $pageIndex => $pageArtists): ?>
                    <!-- Page <?= $pageIndex + 1 ?> -->
                    <div class="carousel-page" data-page="<?= $pageIndex ?>" style="display: <?= $pageIndex === 0 ? 'block' : 'none' ?>;">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 md:gap-8">
                            
                            <?php foreach ($pageArtists as $index => $artist): 
                                $cardIndex = ($pageIndex * $artistsPerPage) + $index;
                            ?>
                                <?php include __DIR__ . '/jazz-artist-card.php'; ?>
                            <?php endforeach; ?>
                            
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                </div>
            </div>
            
            <!-- Left Arrow -->
            <?php if ($totalPages > 1): ?>
            <button 
                type="button"
                id="prev-arrow"
                onclick="changePage(-1)"
                class="absolute left-0 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white border-2 border-gray-900 flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all shadow-lg z-10"
                aria-label="Previous artists">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width: 3;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            
            <!-- Right Arrow -->
            <button 
                type="button"
                id="next-arrow"
                onclick="changePage(1)"
                class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white border-2 border-gray-900 flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all shadow-lg z-10"
                aria-label="Next artists">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width: 3;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <?php endif; ?>
            
        </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="inline-block jazz_event_border_lavender rounded-2xl p-12 bg-white">
                    <div class="text-6xl mb-4">🎺</div>
                    <p class="text-gray-600 text-lg">No artists available at this time.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
let currentPageIndex = 0;
const totalPagesCount = <?= $totalPages ?>;

function changePage(direction) {
    // Calculate new page
    let newPage = currentPageIndex + direction;
    
    // Wrap around
    if (newPage < 0) newPage = totalPagesCount - 1;
    if (newPage >= totalPagesCount) newPage = 0;
    
    goToPage(newPage);
}

function goToPage(pageIndex) {
    // Hide current page
    const currentPage = document.querySelector(`.carousel-page[data-page="${currentPageIndex}"]`);
    if (currentPage) {
        currentPage.style.display = 'none';
    }
    
    // Show new page
    const newPage = document.querySelector(`.carousel-page[data-page="${pageIndex}"]`);
    if (newPage) {
        newPage.style.display = 'block';
    }
    
    // Update index
    currentPageIndex = pageIndex;
}
</script>