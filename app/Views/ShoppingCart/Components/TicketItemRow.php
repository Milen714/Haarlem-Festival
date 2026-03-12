<?php
namespace App\Views\Home\Components;
use App\Models\Schedule;
use App\Models\Media;
use App\Models\Enums\EventType;
use App\Models\Payment\OrderItem;
/** @var OrderItem $item */
$eventLabel = '';
$cardStyles = [];
$cardImage = new Media();
$scheduleRef = new Schedule();
if (isset($scheduleItem) && !empty($scheduleItem)) {
    $scheduleRef = $scheduleItem; 
    $eventType = $scheduleRef->event_category?->type ?? null;
    switch ($eventType) {
        case EventType::Magic:
            $cardStyles = ['side' => 'bg-[var(--home-magic-accent)] dark:bg-[var(--home-magic-accent-muted)]', 'muted' => 'bg-[var(--home-magic-accent-muted)] dark:bg-[var(--home-magic-accent-muted-high-contrast)]', 'text'];
            $cardImage = $scheduleRef->venue?->venue_image ?? new Media();
            $eventLabel = $scheduleRef->event_category?->title ?? '';
            break;
        case EventType::History:
            $cardStyles = ['side' => 'bg-[var(--home-history-accent)] dark:bg-[var(--home-history-accent-muted)]', 'muted' => 'bg-[var(--home-history-accent-muted)] dark:bg-[var(--home-history-accent-muted-high-contrast)]'];
            $cardImage = $scheduleRef->landmark?->landmark_image ?? new Media();
            $eventLabel = 'History';
            break;
        case EventType::Yummy:
            $cardStyles = ['side' => 'bg-[var(--home-yummy-accent)] dark:bg-[var(--home-yummy-accent-muted)]', 'muted' => 'bg-[var(--home-yummy-accent-muted)] dark:bg-[var(--home-yummy-accent-muted-high-contrast)]'];
            $cardImage = $scheduleRef->restaurant?->main_image ?? new Media();
            $eventLabel = 'Yummy';
            break;
        case EventType::Jazz:
            $cardStyles = ['side' => 'bg-[var(--home-jazz-accent)] dark:bg-[var(--home-jazz-accent-muted)]', 'muted' => 'bg-[var(--home-jazz-accent-muted)] dark:bg-[var(--home-jazz-accent-muted-high-contrast)]'];
            $cardImage = $scheduleRef->artist?->profile_image ?? new Media();
            $eventLabel = 'Jazz';
            break;
        case EventType::Dance:
            $cardStyles = ['side' => 'bg-[var(--home-dance-accent)] dark:bg-[var(--home-dance-accent-muted)]', 'muted' => 'bg-[var(--home-dance-accent-muted)] dark:bg-[var(--home-dance-accent-muted-high-contrast)]'];
            $cardImage = $scheduleRef->artist?->profile_image ?? new Media();
            $eventLabel = 'Dance';
            break;
        default:
            $cardStyles = ['side' => 'bg-[var(--home-jazz-accent)] dark:bg-[var(--home-jazz-accent-muted)]', 'muted' => 'bg-[var(--home-jazz-accent-muted)] dark:bg-[var(--home-jazz-accent-muted-high-contrast)]'];
    }
} 

?>



<article class="flex flex-row w-full rounded-lg overflow-hidden shadow-md border border-gray-200">
    <div class="relative calendar-coils w-2 md:w-[0.65rem] <?= $cardStyles['side'] ?> text-transparent flex-shrink-0">
        hh
        <div class="absolute w-[1rem] h-[1rem] -bottom-[-2rem] -left-2.5 bg_colors_home rounded-full hidden md:block">
        </div>
    </div>
    <img loading="lazy" src="<?php echo htmlspecialchars($cardImage->file_path); ?>"
        alt="<?php echo htmlspecialchars($cardImage->alt_text); ?>"
        class="w-20 sm:w-24 md:w-40 lg:w-48 h-auto object-cover flex-shrink-0">

    <ol class="flex flex-col items-center justify-center">
        <li class="flex-grow flex items-center px-2 bg-[--home-dance-accent]">
            <a href="">
                <img src="/Assets/Home/EditIcon.svg" alt="Edit Item Icon">
            </a>
        </li>
        <li class="flex-grow flex items-center px-2 bg-red-400">
            <a href=""><img src="/Assets/Home/DeleteIcon.svg" alt="Delete Item Icon"></a>
        </li>
    </ol>

    <div class="flex flex-col flex-grow min-w-0">

        <div class="flex flex-col py-1">
            <div
                class="ml-2 px-3 md:px-4 py-1 md:py-2 text-center w-min <?= $cardStyles['muted'] ?> border-t border-gray-200 rounded-full">
                <span class="text-black font-semibold text-xs md:text-md"><?= strtoupper($eventLabel) ?></span>
            </div>
            <div class="flex flex-row justify-between gap-3 mt-2">


                <div class="flex pl-2 gap-2 items-center">
                    <div class="h-min px-2 md:px-4 py-2 md:py-4 <?= $cardStyles['muted'] ?> rounded-md flex-shrink-0">
                        <h1 class="font-bold text-lg md:text-2xl text-black text-center">
                            <?= $scheduleItem->start_time?->format('H:i') ?>
                    </div>
                    <div class="flex flex-col">
                        <div>
                            <h2 class="text-black text-sm md:text-lg font-semibold"><?= $scheduleItem->artist->name ?>
                            </h2>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span
                                    class="text-sm text-black"><?php echo htmlspecialchars(
                                    $scheduleItem->venue ? $scheduleItem->venue->name : $scheduleItem->landmark->name) ?></span>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/DurationIconHome.svg" alt="Duration Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span
                                    class="text-sm font-bold text-black whitespace-nowrap"><?= "Duration: " . $scheduleItem->getDurationInMinutes() . " Minutes" ?></span>
                            </div>
                            <div class="flex flex-row gap-2 items-center">
                                <img src="/Assets/Home/PersonIcon.svg" alt="Duration Icon"
                                    class="w-4 h-4 flex-shrink-0">
                                <span
                                    class="text-sm font-bold text-black whitespace-nowrap"><?= "x " . $item->quantity?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <article class="flex-shrink-0 w-20 sm:w-24 md:w-48 pl-2 md:pl-4 border-l border-gray-200">
                    <header class="text-black">
                        <h3 class="font-semibold text-xs md:text-base mb-2">
                            €<?= number_format($ticketType->ticket_scheme->price, 2) ?>
                        </h3>
                    </header>
                </article>
            </div>

        </div>

    </div>

</article>