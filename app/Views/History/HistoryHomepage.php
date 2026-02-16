<?php
namespace App\Views\History;
?>

<div class="antialiased text-ink-800 bg-white">
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>

    <div class="max-w-content">
        <?php include __DIR__ . '/Components/HistoryWelcome.php'; ?>

        <section class="mt-12">
            <h3 class="text-center font-history-serif text-xl md:text-2xl text-ink-900">
                Read about our most beloved landmarks
            </h3>
        </section>

        <?php include __DIR__ . '/Components/HistoryMainLandmarks.php'; ?>
    </div>

    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>
</div>