<?php

namespace App\Views\Jazz\Components;

$colors = ['lavender', 'pink', 'yellow', 'coral'];
$labels = ['Opening Night', 'Main Stage', 'Triple Venue', 'FREE ENTRY'];

$days = [];
$index = 0;

foreach ($scheduleByDate ?? [] as $dateKey => $schedules) {
    if ($dateKey === 'unknown') {
        continue;
    }

    $date = new \DateTime($dateKey);
    $color = $colors[$index % count($colors)];

    usort($schedules, static function ($a, $b) {
        $aTime = $a->start_time ? $a->start_time->getTimestamp() : 0;
        $bTime = $b->start_time ? $b->start_time->getTimestamp() : 0;
        return $aTime <=> $bTime;
    });

    $performances = [];
    $dayVenues = [];

    foreach ($schedules as $schedule) {
        $artistName = $schedule->artist?->name ?? 'Artist TBA';
        $venueName = trim((string) ($schedule->venue?->name ?? ''));
        if ($venueName !== '') {
            $dayVenues[$venueName] = true;
        }

        $start = $schedule->start_time ? $schedule->start_time->format('H:i') : '--:--';
        $performances[] = [
            'start' => $start,
            'artist' => $artistName,
        ];
    }

    $isFreeDay = strtolower($date->format('l')) === 'sunday';

    $days[] = [
        'date' => $date,
        'dateKey' => $dateKey,
        'color' => $color,
        'label' => $isFreeDay ? 'FREE ENTRY' : ($labels[$index] ?? ''),
        'venues' => array_keys($dayVenues),
        'performances' => array_slice($performances, 0, 6),
        'performanceCount' => count($performances),
        'isFree' => $isFreeDay,
    ];

    $index++;
}
?>

<section class="py-10 bg-[#f2f2f4]" aria-labelledby="schedule-heading">
    <article class="container mx-auto px-4">
        <h2 id="schedule-heading" class="text-4xl font-bold text-[#1f1f1f] mb-2"
            style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($scheduleSection->title ?? 'Festival at a Glance') ?>
        </h2>
        <div class="h-[2px] w-56 mb-8 bg-gradient-to-r from-[var(--pastel-yellow)] to-[var(--pastel-lavender)]"></div>

        <?php if (!empty($days)): ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($days as $day):
                    $color = $day['color'];
                    $isLight = $color === 'yellow';
                    $headerTextClass = $isLight ? 'text-gray-800' : 'text-white';
                    $btnTextClass = $isLight ? 'text-gray-800' : 'text-white';
                    $venueText = !empty($day['venues']) ? implode(' / ', $day['venues']) : 'Venue TBA';
                    $dayName = $day['date']->format('l');
                ?>
                    <li
                        class="rounded-lg border-2 jazz_event_border_<?= $color ?> bg-white p-4 shadow-[0_2px_0_rgba(0,0,0,0.04)]">
                        <article class="h-full flex flex-col">
                            <header class="mb-4">
                                <div class="flex items-center gap-3 mb-2">
                                    <time datetime="<?= htmlspecialchars($day['dateKey']) ?>"
                                        class="jazz_event_bg_<?= $color ?> <?= $headerTextClass ?> rounded-md px-3 py-2 leading-none text-center min-w-[56px]">
                                        <span
                                            class="block text-3xl font-extrabold"><?= htmlspecialchars($day['date']->format('d')) ?></span>
                                        <span
                                            class="block text-[10px] tracking-wide font-bold mt-1"><?= strtoupper(htmlspecialchars($day['date']->format('M'))) ?></span>
                                    </time>
                                    <div>
                                        <p class="text-[18px] font-semibold text-[#1f1f1f] leading-tight">
                                            <?= htmlspecialchars($dayName) ?></p>
                                        <p class="text-xs text-[#333333] leading-tight"><?= htmlspecialchars($venueText) ?></p>
                                    </div>
                                </div>
                                <?php if ($day['label'] !== ''): ?>
                                    <p
                                        class="text-[10px] font-bold uppercase tracking-[0.06em] <?= $day['isFree'] ? 'text-[#d64550]' : 'text-[#1f1f1f]' ?>">
                                        <?= htmlspecialchars($day['label']) ?>
                                    </p>
                                <?php endif; ?>
                            </header>

                            <ul class="space-y-1 text-xs text-[#252525] leading-tight mb-4 min-h-[100px]" role="list">
                                <?php foreach ($day['performances'] as $perf): ?>
                                    <li>
                                        • <?= htmlspecialchars($perf['start']) ?> - <?= htmlspecialchars($perf['artist']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <p
                                class="text-center text-xs font-semibold py-2 mb-3 rounded-md jazz_event_bg_<?= $color ?> <?= $btnTextClass ?> opacity-80">
                                <?= $day['isFree'] ? (string) $day['performanceCount'] . ' FREE Shows' : (string) $day['performanceCount'] . ' Performances' ?>
                            </p>

                            <p class="text-[11px] text-[#444] mb-3">
                                <?= $day['isFree'] ? 'City Center • Open Air' : 'From EUR10 • Day Pass EUR35' ?>
                            </p>

                            <a href="/events-jazz/schedule?date=<?= htmlspecialchars($day['dateKey']) ?>"
                                class="mt-auto block text-center jazz_event_bg_<?= $color ?> <?= $btnTextClass ?> text-sm font-semibold py-2 rounded-md hover:opacity-90 transition-opacity">
                                See Schedule >
                            </a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-gray-500 py-12">Schedule coming soon!</p>
        <?php endif; ?>
    </article>
</section>