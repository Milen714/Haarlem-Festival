<?php

namespace App\Views\Jazz;

/**
 * Jazz Schedule page — full festival timetable with day filters and accordion cards.
 *
 * @var array $scheduleByDate Schedule entries grouped by date key (Y-m-d).
 */

/* ── View-layer constants ── */
const SCHEDULE_DAY_COLOR_PALETTE = ['lavender', 'pink', 'yellow', 'coral'];
const SCHEDULE_DAY_EVENT_LABELS  = [
    0 => 'Opening Night',
    1 => 'Main Stage Day',
    2 => '3rd Venue Day',
    3 => 'FREE OUTDOOR FINALE',
];
const SCHEDULE_TEXT_COLOR_BY_DAY = [
    'lavender' => 'text-white',
    'pink'     => 'text-white',
    'yellow'   => 'text-gray-800',
    'coral'    => 'text-white',
];
const HALL_NAMES  = ['Main Hall', 'Second Hall', 'Third Hall'];
const HALL_PRICES = [15, 10, 10];

/* ── Build structured day data from the raw schedule ── */
$festivalDays = [];

try {
    $colorIndex = 0;
    foreach (($scheduleByDate ?? []) as $dateKey => $schedules) {
        $date             = new \DateTime($dateKey);
        $color            = SCHEDULE_DAY_COLOR_PALETTE[$colorIndex % 4];
        $performanceCount = count($schedules);

        $dayVenueNames = [];
        foreach ($schedules as $schedule) {
            if (!empty($schedule->venue?->name)) {
                $dayVenueNames[$schedule->venue->name] = true;
            }
        }

        $festivalDays[] = [
            'date'             => $date,
            'dateKey'          => $dateKey,
            'color'            => $color,
            'textColor'        => SCHEDULE_TEXT_COLOR_BY_DAY[$color],
            'eventLabel'       => SCHEDULE_DAY_EVENT_LABELS[$colorIndex] ?? '',
            'performanceCount' => $performanceCount,
            'schedules'        => $schedules,
            'venueNames'       => array_keys($dayVenueNames),
        ];
        $colorIndex++;
    }
} catch (\Throwable $e) {
    $festivalDays = [];
}

/* ── Aggregate totals for the page subtitle ── */
$uniqueArtistIds = [];
foreach (($scheduleByDate ?? []) as $schedules) {
    foreach ($schedules as $schedule) {
        if (!empty($schedule->artist_id)) {
            $uniqueArtistIds[$schedule->artist_id] = true;
        }
    }
}
$totalArtistCount = count($uniqueArtistIds);
$totalDayCount    = count($festivalDays);
$firstDateLabel   = !empty($festivalDays) ? $festivalDays[0]['date']->format('M j')        : 'July 31';
$lastDateLabel    = !empty($festivalDays) ? end($festivalDays)['date']->format('M j, Y')   : 'Aug 3, 2025';
?>

<main class="bg-white min-h-screen">

    <!-- ===== Breadcrumb + Back navigation ===== -->
    <div class="container mx-auto px-4 pt-6 pb-2">
        <a href="/events-jazz"
            class="inline-flex items-center gap-2 bg-[var(--pastel-lavender)] text-white text-sm font-semibold px-4 py-2 rounded-full hover:opacity-90 transition-opacity mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>

        <nav aria-label="Breadcrumb" class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><a href="/" class="hover:text-gray-700 transition-colors">Festival</a></li>
                <li aria-hidden="true" class="text-gray-400">/</li>
                <li><a href="/events-jazz" class="hover:text-gray-700 transition-colors">Jazz</a></li>
                <li aria-hidden="true" class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium" aria-current="page">Schedule</li>
            </ol>
        </nav>
    </div>

    <!-- ===== Page Header ===== -->
    <header class="container mx-auto px-4 pb-8">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-3"
            style="font-family: 'Cormorant Garamond', serif;">
            Jazz Festival Schedule
            <span class="block h-1 w-48 mt-2 rounded"
                style="background: linear-gradient(to right, var(--pastel-lavender), var(--pastel-pink));"></span>
        </h1>

        <p class="text-gray-600 mt-4 text-base md:text-lg">
            <?= $totalArtistCount > 0 ? $totalArtistCount : '18' ?> artists across
            <?= $totalDayCount    > 0 ? $totalDayCount    : '4' ?> days
            &bull;
            <?= htmlspecialchars($firstDateLabel) ?> &ndash; <?= htmlspecialchars($lastDateLabel) ?>
        </p>
        <p class="text-gray-500 text-sm mt-1">
            Patronaat (Thu&ndash;Sat) &bull; Grote Markt (Sun &ndash; FREE)
        </p>

        <!-- Day filter strip -->
        <div class="flex flex-wrap items-center gap-3 mt-6" role="group" aria-label="Filter schedule by day">
            <button id="btn-all" data-action="filter-by-day" data-filter-all
                class="day-filter-btn px-5 py-2 rounded-full text-sm font-semibold bg-[var(--pastel-lavender)] text-white transition-all">
                All Days
            </button>
            <?php foreach ($festivalDays as $day): ?>
                <button id="btn-<?= htmlspecialchars($day['dateKey']) ?>" data-action="filter-by-day"
                    data-filter-date="<?= htmlspecialchars($day['dateKey']) ?>"
                    class="day-filter-btn px-5 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 text-gray-600 hover:border-[var(--pastel-lavender)] hover:text-[var(--pastel-lavender)] transition-all">
                    <?= htmlspecialchars($day['date']->format('D, M j')) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </header>

    <!-- ===== All-Access Passes Banner ===== -->
    <aside class="container mx-auto px-4 mb-10" aria-label="All-access pass information">
        <div class="bg-[#FFF8E7] border border-[#F5D87E] rounded-lg px-6 py-4 text-sm text-gray-700">
            <p class="inline-flex items-center gap-2 font-semibold text-gray-800 mb-1">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                    <path fill-rule="evenodd"
                        d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"
                        clip-rule="evenodd" />
                </svg>
                All-Access Passes Available:
            </p>
            <p>
                Day Pass: <strong>€35</strong> (all shows one day)
                &bull; Weekend Pass: <strong>€80</strong> (Thu + Fri + Sat all shows)
            </p>
        </div>
    </aside>

    <!-- ===== Day Cards Grid ===== -->
    <section class="container mx-auto px-4 pb-16" aria-label="Festival schedule by day">
        <?php if (empty($festivalDays)): ?>
            <p class="text-center text-gray-500 py-16 text-lg">Schedule coming soon!</p>
        <?php else: ?>
            <ol class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start" id="schedule-grid">

                <?php foreach ($festivalDays as $day):
                    $cardId    = 'card-' . $day['dateKey'];
                    $panelId   = 'panel-' . $day['dateKey'];
                    $isFreeDay = ($day['eventLabel'] === 'FREE OUTDOOR FINALE');
                ?>
                    <li class="day-card rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow jazz_event_border_<?= $day['color'] ?>"
                        data-date="<?= htmlspecialchars($day['dateKey']) ?>">
                        <article>

                            <!-- Day card header — click to expand/collapse -->
                            <button type="button" id="<?= $cardId ?>" data-action="toggle-day-card"
                                data-panel-id="<?= $panelId ?>" aria-expanded="false" aria-controls="<?= $panelId ?>"
                                class="w-full flex items-center gap-4 p-5 text-left hover:bg-gray-50 transition-colors
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)]">

                                <!-- Coloured date badge -->
                                <time datetime="<?= htmlspecialchars($day['dateKey']) ?>"
                                    class="jazz_event_bg_<?= $day['color'] ?> <?= $day['textColor'] ?> rounded-lg flex flex-col items-center justify-center min-w-[64px] h-[72px] px-3 flex-shrink-0">
                                    <span class="text-3xl font-extrabold leading-none"><?= $day['date']->format('j') ?></span>
                                    <span
                                        class="text-xs font-semibold uppercase tracking-wider mt-1"><?= strtoupper($day['date']->format('M')) ?></span>
                                </time>

                                <!-- Day label and performance count -->
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-xl font-bold text-gray-900 leading-tight">
                                        <?= htmlspecialchars($day['date']->format('l')) ?>
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <?php if ($isFreeDay): ?>
                                            <mark
                                                class="bg-[var(--pastel-coral)] text-white text-xs font-bold px-2 py-0.5 rounded not-italic">
                                                FREE OUTDOOR FINALE
                                            </mark>
                                        <?php else: ?>
                                            <?= htmlspecialchars($day['eventLabel']) ?>
                                            &bull;
                                            <span class="font-medium text-gray-700">
                                                <?= $day['performanceCount'] ?>
                                                Performance<?= $day['performanceCount'] !== 1 ? 's' : '' ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <!-- Expand/collapse arrow -->
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform duration-300 toggle-arrow"
                                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Collapsible performance list -->
                            <section id="<?= $panelId ?>" role="region" aria-labelledby="<?= $cardId ?>"
                                class="hidden border-t border-gray-100">
                                <?php if (!empty($day['schedules'])):
                                    /* Group schedule IDs by start time to determine hall position */
                                    $scheduleIdsByTime = [];
                                    foreach ($day['schedules'] as $schedule) {
                                        $timeKey = $schedule->start_time ? $schedule->start_time->format('H:i') : '00:00';
                                        $scheduleIdsByTime[$timeKey][] = $schedule->schedule_id;
                                    }
                                ?>
                                    <ol class="flex flex-col gap-3 p-4"
                                        aria-label="Performances on <?= htmlspecialchars($day['date']->format('l')) ?>">

                                        <?php foreach ($day['schedules'] as $schedule):
                                            $timeKey      = $schedule->start_time ? $schedule->start_time->format('H:i') : '00:00';
                                            $hallPosition = array_search($schedule->schedule_id, $scheduleIdsByTime[$timeKey] ?? []);
                                            $hallName     = HALL_NAMES[$hallPosition]  ?? 'Main Hall';
                                            $ticketPrice  = $isFreeDay ? 0 : (HALL_PRICES[$hallPosition] ?? 10);
                                            $venueName    = $schedule->venue?->name ?? '';
                                            $hallLabel    = $venueName ? $venueName . ' — ' . $hallName : $hallName;

                                            $genreDisplay = '';
                                            $genreRaw     = $schedule->artist?->genres ?? '';
                                            if ($genreRaw) {
                                                $genreParts   = array_filter(array_map('trim', preg_split('/[,;]+/', $genreRaw)));
                                                $genreDisplay = implode(' • ', $genreParts);
                                            }
                                        ?>
                                            <li
                                                class="jazz_event_border_<?= $day['color'] ?> rounded-xl bg-white flex items-center gap-4 px-5 py-4">

                                                <!-- Time column -->
                                                <div class="flex flex-col items-center min-w-[52px] flex-shrink-0">
                                                    <time
                                                        datetime="<?= htmlspecialchars($schedule->start_time ? $schedule->start_time->format('H:i') : '') ?>"
                                                        class="text-2xl font-extrabold text-gray-900 leading-none">
                                                        <?= $schedule->start_time ? htmlspecialchars($schedule->start_time->format('H:i')) : '--:--' ?>
                                                    </time>
                                                    <?php if ($schedule->end_time): ?>
                                                        <time datetime="<?= htmlspecialchars($schedule->end_time->format('H:i')) ?>"
                                                            class="text-xs text-gray-400 mt-1 leading-none">
                                                            <?= htmlspecialchars($schedule->end_time->format('H:i')) ?>
                                                        </time>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Artist info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-bold text-gray-900 text-base leading-snug">
                                                        <?= htmlspecialchars($schedule->artist?->name ?? 'Artist TBA') ?>
                                                    </p>
                                                    <?php if ($genreDisplay): ?>
                                                        <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($genreDisplay) ?></p>
                                                    <?php endif; ?>
                                                    <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($hallLabel) ?></p>
                                                </div>

                                                <!-- Ticket action -->
                                                <?php if (!empty($schedule->is_sold_out)): ?>
                                                    <span
                                                        class="flex-shrink-0 text-xs bg-red-100 text-red-500 font-semibold px-4 py-2 rounded-full">
                                                        Sold Out
                                                    </span>
                                                <?php else: ?>
                                                    <button type="button" data-action="buy-ticket"
                                                        data-schedule-id="<?= (int) $schedule->schedule_id ?>"
                                                        data-artist="<?= htmlspecialchars($schedule->artist?->name ?? 'Artist TBA', ENT_QUOTES) ?>"
                                                        data-date="<?= htmlspecialchars($day['date']->format('l, F j'), ENT_QUOTES) ?>"
                                                        data-start="<?= htmlspecialchars($schedule->start_time ? $schedule->start_time->format('H:i') : '--:--', ENT_QUOTES) ?>"
                                                        data-end="<?= htmlspecialchars($schedule->end_time   ? $schedule->end_time->format('H:i')   : '',       ENT_QUOTES) ?>"
                                                        data-venue="<?= htmlspecialchars($hallLabel, ENT_QUOTES) ?>"
                                                        data-price="<?= $ticketPrice ?>"
                                                        class="flex-shrink-0 jazz_event_pill_<?= $day['color'] ?> cursor-pointer">
                                                        <?= $isFreeDay ? 'Free' : 'Buy &euro;' . $ticketPrice ?>
                                                    </button>
                                                <?php endif; ?>

                                            </li>
                                        <?php endforeach; ?>
                                    </ol>

                                <?php else: ?>
                                    <p class="px-5 py-6 text-sm text-gray-400">No performances scheduled yet.</p>
                                <?php endif; ?>
                            </section>

                        </article>
                    </li>
                <?php endforeach; ?>

            </ol>
        <?php endif; ?>
    </section>

    <!-- ===== Call to Action ===== -->
    <section class="py-16 bg-gray-50 text-center" aria-label="Contact information">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" style="font-family: 'Cormorant Garamond', serif;">
                Ready to Experience Haarlem Jazz?
            </h2>
            <address class="text-gray-500 text-sm not-italic">
                Contact: <a href="mailto:Student@inholland.nl" class="underline hover:text-gray-700 transition-colors">
                    Student@inholland.nl
                </a>
            </address>
        </div>
    </section>

    <?php include __DIR__ . '/Components/Partials/purchase-overlay.php'; ?>

</main>

<?php include __DIR__ . '/Components/Partials/purchase-overlay-js.php'; ?>

<script>
    (function() {
        'use strict';

        /**
         * Toggle the expanded / collapsed state of a day-card accordion.
         *
         * @param {HTMLButtonElement} button  - The header button that was clicked.
         * @param {string}            panelId - ID of the collapsible section to toggle.
         */
        function toggleDayCard(button, panelId) {
            const panel = document.getElementById(panelId);
            const arrow = button.querySelector('.toggle-arrow');
            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            panel.classList.toggle('hidden', isExpanded);
            button.setAttribute('aria-expanded', String(!isExpanded));
            arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        /**
         * Show only day cards that match the selected date, or all cards.
         *
         * @param {string} selectedDateKey - A 'Y-m-d' date string, or the string 'all'.
         */
        function filterScheduleByDay(selectedDateKey) {
            const isShowAll = selectedDateKey === 'all' || !selectedDateKey;
            const allCards = document.querySelectorAll('.day-card');
            const filterBtns = document.querySelectorAll('.day-filter-btn');

            filterBtns.forEach(function(btn) {
                const isActive = isShowAll ?
                    btn.dataset.filterAll !== undefined :
                    btn.dataset.filterDate === selectedDateKey;

                btn.classList.toggle('bg-[var(--pastel-lavender)]', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-transparent', isActive);
                btn.classList.toggle('border-gray-300', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);
            });

            allCards.forEach(function(card) {
                card.style.display = (isShowAll || card.dataset.date === selectedDateKey) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                const target = event.target.closest('[data-action]');
                if (!target) return;

                if (target.dataset.action === 'toggle-day-card') {
                    toggleDayCard(target, target.dataset.panelId);
                }

                if (target.dataset.action === 'filter-by-day') {
                    filterScheduleByDay(target.dataset.filterDate || 'all');
                }
            });
        });
    }());
</script>