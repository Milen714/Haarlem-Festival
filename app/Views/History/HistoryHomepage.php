<?php
namespace App\Views\Home\History;

?>
<h1>Hola, probando mi página</h1>
<section class="flex flex-col gap-6 bg_colors_home text_colors_home pt-4">
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>
    <?php include __DIR__ . '/Components/HistoryWelcome.php'; ?>
    <?php include __DIR__ . '/Components/HistoryMainLandmarks.php'; ?>
    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>
</section>