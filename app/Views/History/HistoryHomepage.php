<?php
namespace App\Views\History;
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)]">
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>

    <div class="max-w-content">
        <?php include __DIR__ . '/Components/HistoryWelcome.php'; ?>

        <section class="mt-12">
            <h2 class="text-center font-serif text-2xl md:text-3xl text-ink-900">
                Read about our most beloved landmarks
            </h3>
            <div class="underline-history mx-auto"></div>
        </section>

        <?php include __DIR__ . '/Components/HistoryMainLandmarks.php'; ?>
    </div>

    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>
</div>
