<?php
namespace App\Views\History;

/** @var string $text */
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)]">
    
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>
    
    <div class="relative">
        <a href="/events-history" class="absolute left-4 top-[7rem] md:top-[8rem] -translate-y-1/2 text-[var(--history-dark-brown)] hover:opacity-70 transition-opacity" aria-label="Back to Haarlem History">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div class="container mx-auto max-w-[1100px] px-4">

            <?php include __DIR__ . '/Components/TourInfo.php'; ?>

            <?php include __DIR__ . '/Components/TourCards.php'; ?>

            <?php include __DIR__ . '/Components/TourGoodToKnow.php'; ?>

            <?php include __DIR__ . '/Components/TourRoute.php'; ?>
        </div>
    </div>

    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>

    <div class="container mx-auto max-w-[1100px] px-4">
    <?php include __DIR__ . '/Components/TourTickets.php'; ?>

    <p class="mt-3 w-[50vw] mx-auto text-center history-emphasis font-extrabold prose-xl text-xl"><?= htmlspecialchars($text->content_html ?? '') ?></p>
    </div>

</div>