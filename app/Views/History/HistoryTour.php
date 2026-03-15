<?php
namespace App\Views\History;
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)]">
    
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>
    
    <div class="container mx-auto max-w-[1100px] px-4">
        
        <?php include __DIR__ . '/Components/TourInfo.php'; ?>
        
        <?php include __DIR__ . '/Components/TourCards.php'; ?>

        <?php include __DIR__ . '/Components/TourGoodToKnow.php'; ?>

        <?php include __DIR__ . '/Components/TourRoute.php'; ?>

        <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>
        
        <?php include __DIR__ . '/Components/TourTickets.php'; ?>

        <p>At the end of the tour you will receive a digital download as a souvenir from Haarlem
           You tour guide will take of it after you’ve enjoyed the city!</p>
    </div>
</div>