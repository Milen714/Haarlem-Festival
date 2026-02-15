<?php
namespace App\Views\Jazz;
echo isset($message) ? htmlspecialchars($message) : '';
?>

<section class="flex flex-col gap-6 bg_colors_jazz text_colors_jazz pt-4">
    <?php include __DIR__ . '/Components/Jazz-hero.php'; ?>
    <?php include __DIR__ . '/Components/Jazz-about.php'; ?>
    <?php include __DIR__ . '/Components/Jazz-carousel.php'; ?>
    <?php include __DIR__ . '/Components/Jazz-schedule.php'; ?>
    <?php include __DIR__ . '/Components/Jazz-venues.php'; ?>
    <?php include __DIR__ . '/Components/Jazz-tickets.php'; ?>
</section>