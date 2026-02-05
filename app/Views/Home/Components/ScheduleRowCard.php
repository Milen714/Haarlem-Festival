<?php
namespace App\Views\Home\Components;
$cardStyles = [];
switch (rand(1, 5)) {
    case 1:
        $cardStyles = ['side' => 'bg-[var(--home-magic-accent)]', 'muted' => 'bg-[var(--home-magic-accent-muted)]'];
        break;
    case 2:
        $cardStyles = ['side' => 'bg-[var(--home-history-accent)]', 'muted' => 'bg-[var(--home-history-accent-muted)]'];
        break;
    case 3:
        $cardStyles = ['side' => 'bg-[var(--home-yummy-accent)]', 'muted' => 'bg-[var(--home-yummy-accent-muted)]'];
        break;
    case 4:
        $cardStyles = ['side' => 'bg-[var(--home-jazz-accent)]', 'muted' => 'bg-[var(--home-jazz-accent-muted)]'];
        break;
    case 5:
        $cardStyles = ['side' => 'bg-[var(--home-dance-accent)]', 'muted' => 'bg-[var(--home-dance-accent-muted)]'];
        break;
    default:
        $cardStyles = ['side' => 'bg-[var(--home-jazz-accent)]', 'muted' => 'bg-[var(--home-jazz-accent-muted)]'];
}
?>



<article class="flex flex-row w-full rounded-lg overflow-hidden shadow-md">
    <div class="calendar-coils w-[0.75rem] <?= $cardStyles['side'] ?> text-transparent flex-shrink-0">hh</div>
    <div class="flex flex-col flex-grow">
        <div class="flex flex-row gap-4 p-4 flex-grow">
            <div class="flex flex-col items-center justify-center gap-4 py-8">
                <div class="w-fit px-4 py-4 <?= $cardStyles['muted'] ?> rounded-md flex-shrink-0">
                    <h1 class="font-bold text-2xl text-black text-center">18.00 – 19.00</h1>
                </div>
            </div>
            <div class="flex flex-col gap-3 flex-grow min-w-0">
                <div>
                    <h2 class="text-black text-lg font-semibold">Haarlem Jazz: Gumbo Kings</h2>
                    <h2 class="text-black text-lg font-semibold">Haarlem Jazz: Wicked Jazz Sounds</h2>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex flex-row gap-2 items-center">
                        <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon"
                            class="w-4 h-4 flex-shrink-0">
                        <span class="text-sm text-black">Main Hall @ Patronaat</span>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon"
                            class="w-4 h-4 flex-shrink-0">
                        <span class="text-sm text-black">Second Hall @ Patronaat</span>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <img src="/Assets/Home/DurationIconHome.svg" alt="Duration Icon" class="w-4 h-4 flex-shrink-0">
                        <span class="text-sm font-bold text-black">Duration: 1 hour</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 w-48 pl-4 border-l border-gray-200">
                <header class="text-black">
                    <h3 class="font-semibold text-base mb-2">Description</h3>
                    <p class="text-sm text-gray-700">Description of the event goes here.</p>
                </header>
            </div>
        </div>
        <div class="p-3 text-center w-full <?= $cardStyles['muted'] ?> border-t border-gray-200">
            <span class="text-black font-semibold">HAARLEM JAZZ!</span>
        </div>
    </div>
    <img src="/Assets/Home/Gumbo.jpg" alt="Gumbo Kings" class="h-auto w-48 object-cover flex-shrink-0 rounded-r-lg">
</article>