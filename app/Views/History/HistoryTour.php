<?php
namespace App\Views\History;

/** @var string $text */

$breadcrumbs = [
    ['label' => 'Home',            'url' => '/'],
    ['label' => 'Haarlem History', 'url' => '/events-history'],
    ['label' => 'History Tour'],
];
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)]">
    
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>

    <?php include __DIR__ . '/Components/breadcrumb.php'; ?>

    <div class="container mx-auto max-w-[1100px] px-4">
        <?php include __DIR__ . '/Components/TourInfo.php'; ?>
    </div>

    <div class="container mx-auto max-w-[1100px] px-4">
        <?php include __DIR__ . '/Components/TourCards.php'; ?>

        <?php include __DIR__ . '/Components/TourGoodToKnow.php'; ?>

        <?php include __DIR__ . '/Components/TourRoute.php'; ?>
    </div>

    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>

    <div class="container mx-auto max-w-[1100px] px-4">
    <?php include __DIR__ . '/Components/TourTickets.php'; ?>

    <p class="mt-3 mb-16 w-[50vw] mx-auto text-center history-emphasis font-extrabold prose-xl text-xl"><?= htmlspecialchars($text->content_html ?? '') ?></p>
    </div>

</div>