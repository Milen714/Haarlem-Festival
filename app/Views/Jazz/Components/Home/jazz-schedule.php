<?php

namespace App\Views\Jazz\Components\Home;

/**
 * Jazz Schedule Overview — "at a glance" day cards on the Jazz home page.
 * Shows a limited preview of each day; links to the full schedule page.
 *
 * @var object|null $scheduleSection CMS section with a title.
 * @var array       $scheduleByDate  Schedule entries grouped by date key (Y-m-d).
 */

/* ── Constants ── */
const OVERVIEW_DAY_COLORS           = ['lavender', 'pink', 'yellow', 'coral'];
const OVERVIEW_DAY_LABELS           = ['Opening Night', 'Main Stage', 'Triple Venue', 'FREE ENTRY'];
const OVERVIEW_MAX_PREVIEW_SHOWS    = 6;

/* ── Build the structured day list from the raw schedule ── */
$overviewDays = [];

try {
    $dayIndex = 0;
    foreach (($scheduleByDate ?? []) as $dateKey => $daySchedules) {
        if ($dateKey === 'unknown') {
            continue;
        }

        $date     = new \DateTime($dateKey);
        $color    = OVERVIEW_DAY_COLORS[$dayIndex % count(OVERVIEW_DAY_COLORS)];
        $isFriday = strtolower($date->format('l')) === 'sunday';

        /* Sort performances by start time ascending */
        usort($daySchedules, static function ($a, $b) {
            $aTimestamp = $a->start_time ? $a->start_time->getTimestamp() : 0;
            $bTimestamp = $b->start_time ? $b->start_time->getTimestamp() : 0;
            return $aTimestamp <=> $bTimestamp;
        });

        $performanceList = [];
        $venueNameMap    = [];

        foreach ($daySchedules as $schedule) {
            $artistName = $schedule->artist?->name ?? 'Artist TBA';
            $venueName  = trim((string) ($schedule->venue?->name ?? ''));
            if ($venueName !== '') {
                $venueNameMap[$venueName] = true;
            }
            $performanceList[] = [
                'startTime'  => $schedule->start_time ? $schedule->start_time->format('H:i') : '--:--',
                'artistName' => $artistName,
            ];
        }

        $overviewDays[] = [
            'date'             => $date,
            'dateKey'          => $dateKey,
            'color'            => $color,
            'eventLabel'       => $isFriday ? 'FREE ENTRY' : (OVERVIEW_DAY_LABELS[$dayIndex] ?? ''),
            'venueNames'       => array_keys($venueNameMap),
            'performances'     => array_slice($performanceList, 0, OVERVIEW_MAX_PREVIEW_SHOWS),
            'performanceCount' => count($performanceList),
            'isFreeDay'        => $isFriday,
        ];

        $dayIndex++;
    }
} catch (\Throwable $e) {
    $overviewDays = [];
}
?>

<section class="py-10 bg-[#f2f2f4]" aria-labelledby="schedule-overview-heading">
    <article class="container mx-auto px-4">

        <h2 id="schedule-overview-heading"
            class="text-4xl font-bold text-[#1f1f1f] mb-2"
            style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($scheduleSection->title ?? 'Festival at a Glance') ?>
        </h2>
        <div class="h-[2px] w-56 mb-8 bg-gradient-to-r from-[var(--pastel-yellow)] to-[var(--pastel-lavender)]"></div>

        <?php if (!empty($overviewDays)): ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <?php foreach ($overviewDays as $day):
                    $isLightColor    = $day['color'] === 'yellow';
                    $headerTextClass = $isLightColor ? 'text-gray-800' : 'text-white';
                    $venueDisplay    = !empty($day['venueNames']) ? implode(' / ', $day['venueNames']) : 'Venue TBA';
                ?>
                <li class="rounded-lg border-2 jazz_event_border_<?= $day['color'] ?> bg-white p-4 shadow-[0_2px_0_rgba(0,0,0,0.04)]">
                    <article class="h-full flex flex-col">

                        <header class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <!-- Date badge -->
                                <time datetime="<?= htmlspecialchars($day['dateKey']) ?>"
                                      class="jazz_event_bg_<?= $day['color'] ?> <?= $headerTextClass ?> rounded-md px-3 py-2 leading-none text-center min-w-[56px]">
                                    <span class="block text-3xl font-extrabold"><?= htmlspecialchars($day['date']->format('d')) ?></span>
                                    <span class="block text-[10px] tracking-wide font-bold mt-1"><?= strtoupper(htmlspecialchars($day['date']->format('M'))) ?></span>
                                </time>
                                <div>
                                    <p class="text-[18px] font-semibold text-[#1f1f1f] leading-tight">
                                        <?= htmlspecialchars($day['date']->format('l')) ?>
                                    </p>
                                    <p class="text-xs text-[#333333] leading-tight"><?= htmlspecialchars($venueDisplay) ?></p>
                                </div>
                            </div>
                            <?php if ($day['eventLabel'] !== ''): ?>
                                <p class="text-[10px] font-bold uppercase tracking-[0.06em] <?= $day['isFreeDay'] ? 'text-[#d64550]' : 'text-[#1f1f1f]' ?>">
                                    <?= htmlspecialchars($day['eventLabel']) ?>
                                </p>
                            <?php endif; ?>
                        </header>

                        <!-- Performance preview list -->
                        <ul class="space-y-1 text-xs text-[#252525] leading-tight mb-4 min-h-[100px]" role="list">
                            <?php foreach ($day['performances'] as $performance): ?>
                                <li>
                                    &bull; <?= htmlspecialchars($performance['startTime']) ?>
                                    &mdash; <?= htmlspecialchars($performance['artistName']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Show count badge -->
                        <p class="text-center text-xs font-semibold py-2 mb-3 rounded-md jazz_event_bg_<?= $day['color'] ?> <?= $headerTextClass ?> opacity-80">
                            <?= $day['isFreeDay']
                                ? $day['performanceCount'] . ' FREE Shows'
                                : $day['performanceCount'] . ' Performances' ?>
                        </p>

                        <!-- Pricing note -->
                        <p class="text-[11px] text-[#444] mb-3">
                            <?= $day['isFreeDay'] ? 'City Centre &bull; Open Air' : 'From &euro;10 &bull; Day Pass &euro;35' ?>
                        </p>

                        <!-- Link to full schedule filtered by this day -->
                        <a href="/events-jazz/schedule?date=<?= htmlspecialchars($day['dateKey']) ?>"
                           class="mt-auto block text-center jazz_event_bg_<?= $day['color'] ?> <?= $headerTextClass ?> text-sm font-semibold py-2 rounded-md hover:opacity-90 transition-opacity">
                            See Full Schedule &rsaquo;
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
