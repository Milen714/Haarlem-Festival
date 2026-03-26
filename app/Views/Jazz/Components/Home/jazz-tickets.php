<?php

namespace App\Views\Jazz\Components\Home;

use App\Models\Enums\TicketSchemeEnum;

/**
 * Jazz Tickets — ticket type pricing cards (single show, day pass, full weekend).
 *
 * @var object|null $ticketsSection  CMS section with a title.
 * @var array       $passTicketTypes Pass-type ticket objects from the service layer.
 */

/* ── Price threshold that distinguishes a full-weekend day pass from a single-day pass ── */
const WEEKEND_PASS_PRICE_THRESHOLD = 60;

/* ── Separate the pass types into day passes and the weekend/all-access pass ── */
$dayPassTicketTypes  = [];
$weekendPassTicket   = null;

foreach (($passTicketTypes ?? []) as $ticketType) {
    $schemeEnumValue = $ticketType->ticket_scheme->scheme_enum->value ?? '';
    $ticketPrice     = (float) ($ticketType->ticket_scheme->price ?? 0);

    if ($schemeEnumValue === TicketSchemeEnum::JAZZ_DAY_PASS->value && $ticketPrice >= WEEKEND_PASS_PRICE_THRESHOLD) {
        $weekendPassTicket = $ticketType;
    } elseif ($schemeEnumValue === TicketSchemeEnum::JAZZ_DAY_PASS->value) {
        $dayPassTicketTypes[] = $ticketType;
    }
}

/* Deduplicate day passes: one entry per calendar day, sorted by date ascending */
$dayPassByDate = [];
foreach ($dayPassTicketTypes as $dayPass) {
    $passDate    = $dayPass->schedule->date ?? $dayPass->schedule->start_time ?? null;
    $dateKey     = $passDate ? $passDate->format('Y-m-d') : 'unknown';
    if (!isset($dayPassByDate[$dateKey])) {
        $dayPassByDate[$dateKey] = $dayPass;
    }
}
ksort($dayPassByDate);
unset($dayPassByDate['unknown']);
$dayPassTicketTypes = array_values($dayPassByDate);
?>

<section class="py-10 bg-white" aria-labelledby="tickets-heading">
    <div class="container mx-auto px-4">

        <h2 id="tickets-heading"
            class="text-2xl md:text-4xl font-bold mb-8 md:mb-12"
            style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($ticketsSection->title ?? 'Tickets & Passes') ?>
        </h2>

        <ul class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">

            <!-- ── Single Show Ticket ── -->
            <li class="jazz_event_border_lavender rounded-lg p-6 bg-white hover:shadow-lg transition-shadow">
                <article>
                    <header>
                        <h3 class="text-xl md:text-2xl font-bold mb-2">Single Show</h3>
                        <p class="text-gray-600 mb-4">Entry to one performance</p>
                    </header>

                    <data value="10-15" class="block mb-6">
                        <span class="text-4xl md:text-5xl font-bold">&euro;10&ndash;15</span>
                        <span class="text-gray-600 block text-sm mt-1">per show</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Choose any show you like
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Main Hall: &euro;15
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Second &amp; Third Hall: &euro;10
                        </li>
                    </ul>

                    <footer>
                        <a href="/events-jazz/schedule" class="block w-full jazz_event_button_lavender text-center">
                            Choose a Show
                        </a>
                    </footer>
                </article>
            </li>

            <!-- ── Day Pass ── -->
            <?php
                $firstDayPass   = $dayPassTicketTypes[0] ?? null;
                $dayPassPrice   = $firstDayPass ? number_format((float) ($firstDayPass->ticket_scheme->price ?? 35), 0) : '35';
                $dayPassName    = $firstDayPass ? htmlspecialchars($firstDayPass->ticket_scheme->name ?? 'Day Pass') : 'Day Pass';
            ?>
            <li class="jazz_event_border_pink rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-3 right-3 jazz_event_bg_pink text-white text-xs font-bold px-2 py-1 rounded-full">
                        BEST VALUE
                    </mark>

                    <header>
                        <h3 class="text-xl md:text-2xl font-bold mb-2"><?= $dayPassName ?></h3>
                        <p class="text-gray-600 mb-4">All shows on one day</p>
                    </header>

                    <data value="<?= $dayPassPrice ?>" class="block mb-6">
                        <span class="text-4xl md:text-5xl font-bold">&euro;<?= $dayPassPrice ?></span>
                        <span class="text-gray-600 block text-sm mt-1">per day &mdash; choose your day</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Unlimited access for one full day
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All 3 halls included
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All venues included
                        </li>
                    </ul>

                    <footer class="space-y-2">
                        <?php if (empty($dayPassTicketTypes)): ?>
                            <button type="button" disabled
                                    class="block w-full jazz_event_button_pink text-center opacity-50 cursor-not-allowed">
                                Coming Soon
                            </button>
                        <?php else: ?>
                            <?php foreach ($dayPassTicketTypes as $dayPass):
                                $passTypeId  = (int) $dayPass->ticket_type_id;
                                $passPrice   = number_format((float) ($dayPass->ticket_scheme->price ?? 35), 0);
                                $passName    = htmlspecialchars($dayPass->ticket_scheme->name ?? 'Day Pass');
                                $passDate    = $dayPass->schedule->date ?? $dayPass->schedule->start_time ?? null;
                                $passLabel   = $passDate ? $passDate->format('l')    : $passName;
                                $passShort   = $passDate ? $passDate->format('D j M') : '';
                                $isSoldOut   = !empty($dayPass->is_sold_out);
                            ?>
                                <?php if ($isSoldOut): ?>
                                <button type="button" disabled
                                        class="block w-full jazz_event_button_pink text-center opacity-50 cursor-not-allowed text-sm py-2">
                                    <?= htmlspecialchars($passLabel) ?> &mdash; Sold Out
                                </button>
                                <?php else: ?>
                                <button type="button"
                                        data-action="buy-ticket"
                                        data-ticket-type-id="<?= $passTypeId ?>"
                                        data-artist="<?= $passName ?>"
                                        data-date="<?= htmlspecialchars($passShort ?: $passLabel) ?>"
                                        data-venue="All Venues"
                                        data-price="<?= $passPrice ?>"
                                        class="block w-full jazz_event_button_pink text-center cursor-pointer text-sm py-2">
                                    Buy <?= htmlspecialchars($passLabel) ?> Pass
                                </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </footer>
                </article>
            </li>

            <!-- ── Weekend / All-Access Pass ── -->
            <?php
                $weekendPrice   = $weekendPassTicket ? number_format((float) ($weekendPassTicket->ticket_scheme->price ?? 80), 0) : '80';
                $weekendName    = $weekendPassTicket ? htmlspecialchars($weekendPassTicket->ticket_scheme->name ?? 'Weekend Pass') : 'Weekend Pass';
                $weekendTypeId  = $weekendPassTicket ? (int) $weekendPassTicket->ticket_type_id : null;
                $weekendSoldOut = $weekendPassTicket ? !empty($weekendPassTicket->is_sold_out) : false;
            ?>
            <li class="jazz_event_border_yellow rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-3 right-3 jazz_event_bg_yellow text-gray-800 text-xs font-bold px-2 py-1 rounded-full">
                        SAVE &euro;25
                    </mark>

                    <header>
                        <h3 class="text-xl md:text-2xl font-bold mb-2"><?= $weekendName ?></h3>
                        <p class="text-gray-600 mb-4">Four full days of access</p>
                    </header>

                    <data value="<?= $weekendPrice ?>" class="block mb-6">
                        <span class="text-4xl md:text-5xl font-bold">&euro;<?= $weekendPrice ?></span>
                        <span class="text-gray-600 block text-sm mt-1">Thu + Fri + Sat + Sun</span>
                    </data>

                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All indoor shows included
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All venues &amp; stages
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
                                    data-action="buy-ticket"
                                    data-ticket-type-id="<?= $weekendTypeId ?>"
                                    data-artist="<?= $weekendName ?>"
                                    data-date="Thu + Fri + Sat + Sun — All Indoor Shows"
                                    data-venue="All Venues &amp; Stages"
                                    data-price="<?= $weekendPrice ?>"
                                    class="block w-full jazz_event_button_yellow text-center cursor-pointer">
                                Buy <?= $weekendName ?>
                            </button>
                        <?php endif; ?>
                    </footer>
                </article>
            </li>

        </ul>

        <!-- Free outdoor day notice -->
        <footer class="text-center mt-8 md:mt-12">
            <p class="inline-flex items-center text-gray-700 bg-gray-50 px-4 py-3 rounded-lg text-sm md:text-base">
                <svg class="w-5 h-5 mr-2 text-[var(--pastel-coral)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <strong>Sunday at Grote Markt is FREE — no tickets needed!</strong>
            </p>
            <address class="text-sm text-gray-600 mt-3 not-italic">
                Contact:
                <a href="mailto:tickets@haarlemfestival.nl"
                   class="text-[var(--pastel-lavender)] hover:underline">
                    tickets@haarlemfestival.nl
                </a>
            </address>
        </footer>

    </div>
</section>
