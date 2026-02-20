<?php
namespace App\Views\Magic\Components;

$day = $day ?? 'Friday';
$title = $title ?? 'The Lorentz Formula';
$duration = $duration ?? 'Approx. 50 minutes';
$showtimes = $showtimes ?? ['12:30 - 13:20', '14:00 - 14:50', '15:00 - 15:50'];
?>

<section class="flex flex-row border-2 border-[var(--magic-bright-gold-accent)] rounded-lg w-full">
    <section
        class="flex flex-col md:flex-row border border-[var(--magic-border-transperent-dark)] w-full rounded-lg overflow-hidden">
        <div
            class="flex flex-col items-center justify-center h-full p-4 md:p-5 text-xl md:text-2xl text-[var(--magic-bright-gold-accent)] font-courierprime bg-[var(--magic-bg-secondary-dark)] md:w-28">
            <h3><?= $day ?></h3>
        </div>
        <div class="w-full text-center px-3 md:px-0">
            <h2 class="text-2xl md:text-3xl font-courierprime text-[var(--magic-gold-accent)] mb-2 md:mb-3">
                <?= $title ?></h2>
            <div class="flex flex-col md:flex-row justify-between gap-2 px-2 md:p-4">
                <p class="font-robotomono text-base md:text-lg mb-1 md:mb-3">Showtimes:</p>
                <p class="font-robotomono text-base md:text-lg mb-2 md:mb-3">Duration: <?= $duration ?></p>
            </div>
            <ul
                class="flex flex-wrap items-center justify-center md:justify-around gap-3 md:gap-6 font-robotomono text-base md:text-lg px-2 md:px-4 pb-4">
                <?php foreach ($showtimes as $time): ?>
                <li class="px-2 py-2 md:py-3 border border-[var(--magic-creme-gold-accent)] rounded-lg">
                    <p class="flex flex-row gap-2 items-center">
                        <img src="/Assets/Magic/ClockIcon.svg" alt=""> <?= $time ?>
                    </p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

</section>