<?php

namespace App\Views\Jazz\Components;
?>

<section class="py-10 bg-white" aria-labelledby="tickets-heading">
    <div class="container mx-auto px-4">
        <h2 id="tickets-heading" class="text-4xl font-bold mb-12" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($ticketsSection->title ?? 'Tickets & Passes') ?>
        </h2>

        <?php
        $passTicketTypes = $passTicketTypes ?? [];
        $dayPassTypes    = [];
        $weekendPassType = null;
        foreach ($passTicketTypes as $pt) {
            $enum  = $pt->ticket_scheme->scheme_enum->value ?? '';
            $price = (float)($pt->ticket_scheme->price ?? 0);
            if ($enum === 'JAZZ_WEEKEND_PASS') {
                $weekendPassType = $pt;
            } elseif ($enum === 'JAZZ_DAY_PASS' && $price >= 60) {
                // High-price day pass doubles as the all-access / weekend pass
                $weekendPassType = $pt;
            } elseif ($enum === 'JAZZ_DAY_PASS') {
                $dayPassTypes[] = $pt;
            }
        }
        // Deduplicate: one entry per unique calendar day (prefer date, fall back to start_time), sorted ascending
        $dayPassByDate = [];
        foreach ($dayPassTypes as $dp) {
            $dt  = $dp->schedule->date ?? $dp->schedule->start_time ?? null;
            $key = $dt ? $dt->format('Y-m-d') : 'unknown';
            if (!isset($dayPassByDate[$key])) {
                $dayPassByDate[$key] = $dp;
            }
        }
        ksort($dayPassByDate);
        unset($dayPassByDate['unknown']);
        $dayPassTypes = array_values($dayPassByDate);
        ?>
        <ul class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Single Show Ticket (Lavender) -->
            <li class="jazz_event_border_lavender rounded-lg p-6 bg-white hover:shadow-lg transition-shadow">
                <article>
                    <header>
                        <h3 class="text-2xl font-bold mb-2">Single Show</h3>
                        <p class="text-gray-600 mb-4">Pay just one performance</p>
                    </header>

                    <data value="10-15" class="block mb-6">
                        <span class="text-5xl font-bold">€10-15</span>
                        <span class="text-gray-600 block text-sm mt-1">per show</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Choose your shows
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Main Hall: €15
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Second/Third Hall: €10
                        </li>
                    </ul>

                    <footer>
                        <a href="/events-jazz/schedule"
                           class="block w-full jazz_event_button_lavender text-center">
                            Choose a Show
                        </a>
                    </footer>
                </article>
            </li>

            <!-- Day Pass (Pink) -->
            <?php
                $firstDay  = $dayPassTypes[0] ?? null;
                $dayPrice  = $firstDay ? number_format((float)($firstDay->ticket_scheme->price ?? 35), 0) : '35';
                $dayName   = $firstDay ? htmlspecialchars($firstDay->ticket_scheme->name ?? 'Day Pass') : 'Day Pass';
            ?>
            <li class="jazz_event_border_pink rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-4 right-4 jazz_event_bg_pink text-white text-xs font-bold px-3 py-1 rounded-full">
                        BEST VALUE
                    </mark>

                    <header>
                        <h3 class="text-2xl font-bold mb-2"><?= $dayName ?></h3>
                        <p class="text-gray-600 mb-4">All shows on one day</p>
                    </header>

                    <data value="<?= $dayPrice ?>" class="block mb-6">
                        <span class="text-5xl font-bold">€<?= $dayPrice ?></span>
                        <span class="text-gray-600 block text-sm mt-1">per day — choose your day</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Unlimited access for one day
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            All 3 halls per night
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            All venues included
                        </li>
                    </ul>

                    <footer class="space-y-2">
                        <?php if (empty($dayPassTypes)): ?>
                        <button type="button" disabled
                                class="block w-full jazz_event_button_pink text-center opacity-50 cursor-not-allowed">
                            Coming Soon
                        </button>
                        <?php else: ?>
                            <?php foreach ($dayPassTypes as $dp):
                                $dpId      = (int)$dp->ticket_type_id;
                                $dpPrice   = number_format((float)($dp->ticket_scheme->price ?? 35), 0);
                                $dpName    = htmlspecialchars($dp->ticket_scheme->name ?? 'Day Pass');
                                $dpDate    = $dp->schedule->date ?? $dp->schedule->start_time ?? null;
                                $dpLabel   = $dpDate ? $dpDate->format('l') : $dpName;
                                $dpShort   = $dpDate ? $dpDate->format('D j M') : '';
                                $dpSoldOut = !empty($dp->is_sold_out);
                            ?>
                            <?php if ($dpSoldOut): ?>
                            <button type="button" disabled
                                    class="block w-full jazz_event_button_pink text-center opacity-50 cursor-not-allowed text-sm py-2">
                                <?= $dpLabel ?> — Sold Out
                            </button>
                            <?php else: ?>
                            <button type="button"
                                    class="block w-full jazz_event_button_pink text-center cursor-pointer text-sm py-2"
                                    onclick="buyTicket(this)"
                                    data-ticket-type-id="<?= $dpId ?>"
                                    data-artist="<?= $dpName ?>"
                                    data-date="<?= htmlspecialchars($dpShort ?: $dpLabel) ?>"
                                    data-venue="All Venues"
                                    data-price="<?= $dpPrice ?>">
                                Buy <?= $dpLabel ?> Pass
                            </button>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </footer>
                </article>
            </li>

            <!-- Weekend Pass (Yellow) -->
            <?php
                $weekendPrice   = $weekendPassType ? number_format((float)($weekendPassType->ticket_scheme->price ?? 80), 0) : '80';
                $weekendName    = $weekendPassType ? htmlspecialchars($weekendPassType->ticket_scheme->name ?? 'Weekend Pass') : 'Weekend Pass';
                $weekendTypeId  = $weekendPassType ? (int)$weekendPassType->ticket_type_id : null;
                $weekendSoldOut = $weekendPassType ? !empty($weekendPassType->is_sold_out) : false;
            ?>
            <li class="jazz_event_border_yellow rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-4 right-4 jazz_event_bg_yellow text-gray-800 text-xs font-bold px-3 py-1 rounded-full">
                        SAVE €25
                    </mark>

                    <header>
                        <h3 class="text-2xl font-bold mb-2"><?= $weekendName ?></h3>
                        <p class="text-gray-600 mb-4">Four full days of access</p>
                    </header>

                    <data value="<?= $weekendPrice ?>" class="block mb-6">
                        <span class="text-5xl font-bold">€<?= $weekendPrice ?></span>
                        <span class="text-gray-600 block text-sm mt-1">Thu + Fri + Sat + Sun</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            All 19 indoor shows
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            All venues &amp; stages
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Priority entry
                        </li>
                    </ul>

                    <footer>
                        <?php if ($weekendSoldOut): ?>
                        <button type="button" disabled
                                class="block w-full jazz_event_button_yellow text-center opacity-50 cursor-not-allowed">
                            Sold Out
                        </button>
                        <?php elseif ($weekendTypeId === null): ?>
                        <button type="button" disabled
                                class="block w-full jazz_event_button_yellow text-center opacity-50 cursor-not-allowed">
                            Coming Soon
                        </button>
                        <?php else: ?>
                        <button type="button"
                                class="block w-full jazz_event_button_yellow text-center cursor-pointer"
                                onclick="buyTicket(this)"
                                data-ticket-type-id="<?= $weekendTypeId ?>"
                                data-artist="<?= $weekendName ?>"
                                data-date="Thu + Fri + Sat + Sun — All Indoor Shows"
                                data-venue="All Venues &amp; Stages"
                                data-price="<?= $weekendPrice ?>">
                            Buy <?= $weekendName ?>
                        </button>
                        <?php endif; ?>
                    </footer>
                </article>
            </li>

        </ul>

        <!-- Footer Note -->
        <footer class="text-center mt-12">
            <p class="inline-flex items-center text-gray-700 bg-gray-50 px-6 py-3 rounded-lg">
                <svg class="w-5 h-5 mr-2 text-[var(--pastel-coral)]" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <strong>Sunday at Grote Markt is FREE - no tickets needed!</strong>
            </p>
            <address class="text-sm text-gray-600 mt-3 not-italic">
                Contact: <a href="mailto:tickets@haarlemfestival.nl"
                    class="text-[var(--pastel-lavender)] hover:underline">tickets@haarlemfestival.nl</a>
            </address>
        </footer>
    </div>
</section>