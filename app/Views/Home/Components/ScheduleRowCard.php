<?php
namespace App\Views\Home\Components;
use App\Models\Schedule;
use App\Models\Media;
use App\Models\Enums\EventType;
$eventLabel = '';
$cardStyles = [];
$cardImage = new Media();
$scheduleRef = new Schedule();
if (isset($scheduleItem) && !empty($scheduleItem)) {
    $scheduleRef = $scheduleItem; 
    $eventType = $scheduleRef->event_category?->type ?? null;
    switch ($eventType) {
        case EventType::Magic:
            $cardStyles = ['side' => 'bg-[var(--home-magic-accent)] dark:bg-[var(--home-magic-accent-muted)]', 'muted' => 'bg-[var(--home-magic-accent-muted)] dark:bg-[var(--home-magic-accent-muted-high-contrast)]'];
            $cardImage = $scheduleRef->venue?->venue_image ?? new Media();
            $eventLabel = 'Magic';
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



<article class="flex flex-row w-full rounded-lg overflow-hidden shadow-md">
    <div class="calendar-coils w-[0.75rem] <?= $cardStyles['side'] ?> text-transparent flex-shrink-0">hh</div>
    <div class="flex flex-col flex-grow">
        <div class="flex flex-row gap-4 p-4 flex-grow">
            <div class="flex flex-col items-center justify-center gap-4 py-8">
                <div class="w-fit px-4 py-4 <?= $cardStyles['muted'] ?> rounded-md flex-shrink-0">
                    <h1 class="font-bold text-2xl text-black text-center">
                        <?= $scheduleItem->start_time?->format('H:i') ?> –
                        <?= $scheduleItem->end_time?->format('H:i') ?></h1>
                </div>
            </div>
            <div class="flex flex-col gap-3 flex-grow min-w-0">
                <div>
                    <h2 class="text-black text-lg font-semibold"><?= $scheduleItem->artist->name ?></h2>
                    <h2 class="text-black text-lg font-semibold">Haarlem Jazz: Wicked Jazz Sounds</h2>
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
                        <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon"
                            class="w-4 h-4 flex-shrink-0">
                        <span class="text-sm text-black">Second Hall @ Patronaat</span>
                    </div>
                    <div class="flex flex-row gap-2 items-center">
                        <img src="/Assets/Home/DurationIconHome.svg" alt="Duration Icon" class="w-4 h-4 flex-shrink-0">
                        <span class="text-sm font-bold text-black">Duration:
                            <?= $scheduleItem->getDurationInMinutes() ?> Minutes</span>
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
            <span class="text-black font-semibold"><?= $eventLabel ?></span>
        </div>
    </div>
    <img loading="lazy" src="<?php echo htmlspecialchars($cardImage->file_path); ?>"
        alt="<?php echo htmlspecialchars($cardImage->alt_text); ?>"
        class="h-auto w-48 object-cover flex-shrink-0 rounded-r-lg">
</article>