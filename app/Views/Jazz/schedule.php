<?php
namespace App\Views\Jazz;

$colorMap   = ['lavender', 'pink', 'yellow', 'coral'];
$labelMap   = [
    0 => 'Opening Night',
    1 => 'Main Stage Day',
    2 => '3rd Venue Day',
    3 => 'FREE OUTDOOR FINALE',
];
$textMap    = [
    'lavender' => 'text-white',
    'pink'     => 'text-white',
    'yellow'   => 'text-gray-800',
    'coral'    => 'text-white',
];

// Build day data from schedule
$days = [];
$i    = 0;
foreach ($scheduleByDate as $dateKey => $schedules) {
    $dt                   = new \DateTime($dateKey);
    $color                = $colorMap[$i % 4];
    $performanceCount     = count($schedules);

    $dayVenues = [];
    foreach ($schedules as $s) {
        if ($s->venue && $s->venue->name) {
            $dayVenues[$s->venue->name] = true;
        }
    }

    $days[] = [
        'date'       => $dt,
        'dateKey'    => $dateKey,
        'color'      => $color,
        'textColor'  => $textMap[$color],
        'label'      => $labelMap[$i] ?? '',
        'count'      => $performanceCount,
        'schedules'  => $schedules,
        'venues'     => array_keys($dayVenues),
    ];
    $i++;
}

// Totals for subtitle
$totalArtists = 0;
$artistIds    = [];
foreach ($scheduleByDate as $schedules) {
    foreach ($schedules as $s) {
        if ($s->artist_id && !in_array($s->artist_id, $artistIds)) {
            $artistIds[]   = $s->artist_id;
            $totalArtists++;
        }
    }
}
$totalDays = count($days);

// Date range
$firstDate = !empty($days) ? $days[0]['date']->format('M j') : 'July 31';
$lastDate  = !empty($days) ? end($days)['date']->format('M j, Y') : 'Aug 3, 2025';
?>

<main class="bg-white min-h-screen">

    <!-- ===== Breadcrumb + Page Header ===== -->
    <div class="container mx-auto px-4 pt-6 pb-2">
        <!-- Back button -->
        <a href="/events-jazz"
           class="inline-flex items-center gap-2 bg-[var(--pastel-lavender)] text-white text-sm font-semibold px-4 py-2 rounded-full hover:opacity-90 transition-opacity mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        <!-- Breadcrumb -->
        <nav aria-label="Breadcrumb" class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><a href="/" class="hover:text-gray-700 transition-colors">Festival</a></li>
                <li aria-hidden="true" class="text-gray-400">/</li>
                <li><a href="/" class="hover:text-gray-700 transition-colors">Home</a></li>
                <li aria-hidden="true" class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium" aria-current="page">Schedule</li>
            </ol>
        </nav>
    </div>

    <!-- ===== Hero Title Section ===== -->
    <header class="container mx-auto px-4 pb-8">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-3"
            style="font-family: 'Cormorant Garamond', serif;">
            Jazz Festival Schedule
            <span class="block h-1 w-48 mt-2 rounded"
                  style="background: linear-gradient(to right, var(--pastel-lavender), var(--pastel-pink));"></span>
        </h1>

        <p class="text-gray-600 mt-4 text-base md:text-lg">
            <?= $totalArtists > 0 ? $totalArtists : '18' ?> artists across <?= $totalDays > 0 ? $totalDays : '4' ?> days
            &bull; <?= htmlspecialchars($firstDate) ?> &ndash; <?= htmlspecialchars($lastDate) ?>
        </p>
        <p class="text-gray-500 text-sm mt-1">
            Patronaat (Thu&ndash;Sat) &bull; Grote Markt (Sun &ndash; FREE)
        </p>

        <!-- Day filter strip -->
        <div class="flex flex-wrap items-center gap-3 mt-6" role="group" aria-label="Filter by day">
            <button id="btn-all"
                    onclick="filterDay('all')"
                    class="day-filter-btn px-5 py-2 rounded-full text-sm font-semibold bg-[var(--pastel-lavender)] text-white transition-all">
                All Days
            </button>
            <?php foreach ($days as $idx => $day): ?>
            <button id="btn-<?= htmlspecialchars($day['dateKey']) ?>"
                    onclick="filterDay('<?= htmlspecialchars($day['dateKey']) ?>')"
                    class="day-filter-btn px-5 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 text-gray-600 hover:border-[var(--pastel-lavender)] hover:text-[var(--pastel-lavender)] transition-all">
                <?= htmlspecialchars($day['date']->format('D, M j')) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </header>

    <!-- ===== All-Access Passes Banner ===== -->
    <aside class="container mx-auto px-4 mb-10"
           aria-label="All-Access pass information">
        <div class="bg-[#FFF8E7] border border-[#F5D87E] rounded-lg px-6 py-4 text-sm text-gray-700">
            <span class="inline-flex items-center gap-2 font-semibold text-gray-800 mb-1">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                All-Access Passes Available:
            </span>
            <p>
                Day Pass: <strong>€35</strong> (all shows one day)
                &bull; Weekend Pass: <strong>€80</strong> (Thu + Fri + Sat all shows)
            </p>
        </div>
    </aside>

    <!-- ===== Day Cards Grid ===== -->
    <section class="container mx-auto px-4 pb-16" aria-label="Festival schedule by day">
        <?php if (empty($days)): ?>
            <p class="text-center text-gray-500 py-16 text-lg">Schedule coming soon!</p>
        <?php else: ?>
        <ol class="grid grid-cols-1 md:grid-cols-2 gap-6" id="schedule-grid">

            <?php foreach ($days as $idx => $day):
                $color     = $day['color'];
                $textColor = $day['textColor'];
                $cardId    = 'card-' . $day['dateKey'];
                $panelId   = 'panel-' . $day['dateKey'];
                $isFree    = ($day['label'] === 'FREE OUTDOOR FINALE');
            ?>
            <li class="day-card rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow jazz_event_border_<?= $color ?>"
                data-date="<?= htmlspecialchars($day['dateKey']) ?>">
                <article>
                    <!-- Day card header (always visible, clickable) -->
                    <button type="button"
                            class="w-full flex items-center gap-4 p-5 text-left hover:bg-gray-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)]"
                            aria-expanded="false"
                            aria-controls="<?= $panelId ?>"
                            id="<?= $cardId ?>"
                            onclick="toggleDay(this, '<?= $panelId ?>')">

                        <!-- Colored date box -->
                        <time datetime="<?= htmlspecialchars($day['dateKey']) ?>"
                              class="jazz_event_bg_<?= $color ?> <?= $textColor ?> rounded-lg flex flex-col items-center justify-center min-w-[64px] h-[72px] px-3 flex-shrink-0">
                            <span class="text-3xl font-extrabold leading-none"><?= $day['date']->format('j') ?></span>
                            <span class="text-xs font-semibold uppercase tracking-wider mt-1"><?= strtoupper($day['date']->format('M')) ?></span>
                        </time>

                        <!-- Day info -->
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-bold text-gray-900 leading-tight">
                                <?= htmlspecialchars($day['date']->format('l')) ?>
                            </h2>
                            <p class="text-sm text-gray-500 mt-0.5">
                                <?php if ($isFree): ?>
                                    <mark class="bg-[var(--pastel-coral)] text-white text-xs font-bold px-2 py-0.5 rounded not-italic">
                                        FREE OUTDOOR FINALE
                                    </mark>
                                <?php else: ?>
                                    <?= htmlspecialchars($day['label']) ?>
                                    &bull;
                                    <span class="font-medium text-gray-700">
                                        <?= $day['count'] ?> Performance<?= $day['count'] !== 1 ? 's' : '' ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Toggle arrow -->
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform duration-300 toggle-arrow"
                             fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- Collapsible performance list -->
                    <section id="<?= $panelId ?>"
                             role="region"
                             aria-labelledby="<?= $cardId ?>"
                             class="hidden border-t border-gray-100">
                        <?php if (!empty($day['schedules'])):

                            // Group by start_time to determine hall position (use IDs — not object refs)
                            $byTime = [];
                            foreach ($day['schedules'] as $s) {
                                $t = $s->start_time ? $s->start_time->format('H:i') : '00:00';
                                $byTime[$t][] = $s->schedule_id;
                            }
                            $hallNames  = ['Main Hall', 'Second Hall', 'Third Hall'];
                            $hallPrices = [15, 10, 10];
                        ?>
                        <ol class="flex flex-col gap-3 p-4"
                            aria-label="Performances on <?= htmlspecialchars($day['date']->format('l')) ?>">
                            <?php foreach ($day['schedules'] as $schedule):
                                $timeKey  = $schedule->start_time ? $schedule->start_time->format('H:i') : '00:00';
                                $position = array_search($schedule->schedule_id, $byTime[$timeKey] ?? []);
                                $hallName = $hallNames[$position] ?? 'Main Hall';
                                $price    = $isFree ? 0 : ($hallPrices[$position] ?? 10);
                                $venueName = $schedule->venue?->name ?? '';
                                $hallLabel = $venueName ? $venueName . ' ' . $hallName : $hallName;

                                // Genre string — split on comma or semicolon into bullet-joined tags
                                $genreRaw  = $schedule->artist?->genres ?? '';
                                $genreParts = $genreRaw
                                    ? array_map('trim', preg_split('/[,;]+/', $genreRaw))
                                    : [];
                                $genreStr  = implode(' • ', array_filter($genreParts));
                            ?>
                            <li class="jazz_event_border_<?= $color ?> rounded-xl bg-white flex items-center gap-4 px-5 py-4">

                                <!-- Time column -->
                                <div class="flex flex-col items-center min-w-[52px] flex-shrink-0">
                                    <time datetime="<?= htmlspecialchars($schedule->start_time ? $schedule->start_time->format('H:i') : '') ?>"
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
                                    <?php if ($genreStr): ?>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <?= htmlspecialchars($genreStr) ?>
                                    </p>
                                    <?php endif; ?>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <?= htmlspecialchars($hallLabel) ?>
                                    </p>
                                </div>

                                <!-- Buy / Free button -->
                                <?php if (!empty($schedule->is_sold_out)): ?>
                                    <span class="flex-shrink-0 text-xs bg-red-100 text-red-500 font-semibold px-4 py-2 rounded-full">
                                        Sold Out
                                    </span>
                                <?php elseif ($isFree): ?>
                                    <span class="flex-shrink-0 jazz_event_pill_<?= $color ?>">
                                        Free
                                    </span>
                                <?php else: ?>
                                    <button type="button"
                                            class="flex-shrink-0 jazz_event_pill_<?= $color ?> cursor-pointer"
                                            onclick="buyTicket(this)"
                                            data-schedule-id="<?= (int) $schedule->schedule_id ?>"
                                            data-artist="<?= htmlspecialchars($schedule->artist?->name ?? 'Artist TBA', ENT_QUOTES) ?>"
                                            data-date="<?= htmlspecialchars($day['date']->format('l, F j'), ENT_QUOTES) ?>"
                                            data-start="<?= htmlspecialchars($schedule->start_time ? $schedule->start_time->format('H:i') : '--:--', ENT_QUOTES) ?>"
                                            data-end="<?= htmlspecialchars($schedule->end_time ? $schedule->end_time->format('H:i') : '', ENT_QUOTES) ?>"
                                            data-venue="<?= htmlspecialchars($hallLabel, ENT_QUOTES) ?>"
                                            data-price="<?= (int) $price ?>">
                                        Buy &euro;<?= $price ?>
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

    <!-- ===== CTA Section ===== -->
    <section class="py-16 bg-gray-50 text-center" aria-label="Call to action">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" style="font-family: 'Cormorant Garamond', serif;">
                Ready to Experience Haarlem Jazz?
            </h2>
            <p class="text-gray-500 mb-1 text-sm">
                Contact: <a href="mailto:Student@inholland.nl" class="underline hover:text-gray-700 transition-colors">Student@inholland.nl</a>
            </p>
        </div>
    </section>

    <!-- ===== Ticket Confirmation Modal ===== -->
    <?php include __DIR__ . '/Components/jazz-ticket-modal.php'; ?>

</main>

<?php include __DIR__ . '/Components/jazz-ticket-modal-js.php'; ?>

<script>
/* ─────────────────────────────────────────
   Day accordion & filter
   ───────────────────────────────────────── */

/**
 * Toggle the expanded/collapsed state of a day card.
 * @param {HTMLButtonElement} btn   - The trigger button
 * @param {string}           panelId - The id of the panel to toggle
 */
function toggleDay(btn, panelId) {
    const panel  = document.getElementById(panelId);
    const arrow  = btn.querySelector('.toggle-arrow');
    const isOpen = btn.getAttribute('aria-expanded') === 'true';

    if (isOpen) {
        panel.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        arrow.style.transform = 'rotate(0deg)';
    } else {
        panel.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        arrow.style.transform = 'rotate(180deg)';
    }
}

/**
 * Filter the day cards to show only a specific date or all.
 * @param {string} dateKey - 'all' or a 'Y-m-d' date string
 */
function filterDay(dateKey) {
    const cards   = document.querySelectorAll('.day-card');
    const buttons = document.querySelectorAll('.day-filter-btn');

    // Update active button style
    buttons.forEach(btn => {
        const isActive = (dateKey === 'all' && btn.id === 'btn-all') ||
                         btn.id === 'btn-' + dateKey;
        if (isActive) {
            btn.classList.add('bg-[var(--pastel-lavender)]', 'text-white', 'border-transparent');
            btn.classList.remove('border-gray-300', 'text-gray-600');
        } else {
            btn.classList.remove('bg-[var(--pastel-lavender)]', 'text-white', 'border-transparent');
            btn.classList.add('border-gray-300', 'text-gray-600');
        }
    });

    // Show / hide cards
    cards.forEach(card => {
        if (dateKey === 'all' || card.dataset.date === dateKey) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
