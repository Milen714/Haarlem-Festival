<?php

namespace App\Views\Jazz\Components\ArtistDetail;

use App\Models\Enums\TicketSchemeEnum;

/**
 * Artist Detail Passes — compact day-pass / weekend-pass upsell strip.
 *
 * @var array                                           $passTicketTypes  TicketType objects (JAZZ_DAY_PASS / JAZZ_WEEKEND_PASS schemes).
 * @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm
 */

const ARTIST_DETAIL_WEEKEND_PRICE_THRESHOLD = 60;

/* Dates on which this artist actually performs */
$performanceDates = array_fill_keys(array_keys($vm->scheduleByDate ?? []), true);

$adDayPasses    = [];
$adWeekendPass  = null;

foreach (($passTicketTypes ?? []) as $tt) {
    $enumVal = $tt->ticket_scheme->scheme_enum->value ?? '';
    $price   = (float) ($tt->ticket_scheme->price ?? 0);

    if ($enumVal === TicketSchemeEnum::JAZZ_DAY_PASS->value && $price >= ARTIST_DETAIL_WEEKEND_PRICE_THRESHOLD) {
        $adWeekendPass = $tt;
    } elseif ($enumVal === TicketSchemeEnum::JAZZ_DAY_PASS->value) {
        $passDate = $tt->schedule->date ?? $tt->schedule->start_time ?? null;
        $dateKey  = $passDate ? $passDate->format('Y-m-d') : 'unknown';
        /* Only include the day pass if the artist performs on that day */
        if (isset($performanceDates[$dateKey]) && !isset($adDayPasses[$dateKey])) {
            $adDayPasses[$dateKey] = $tt;
        }
    }
}
ksort($adDayPasses);
$adDayPasses = array_values($adDayPasses);

if (empty($adDayPasses) && $adWeekendPass === null) {
    return;
}
?>

<section aria-labelledby="passes-heading" class="border-t border-gray-100 pt-8">
    <h2 id="passes-heading"
        class="text-2xl md:text-3xl font-bold mb-2"
        style="font-family: 'Cormorant Garamond', serif;">
        Want More Jazz?
    </h2>
    <p class="text-gray-600 mb-6 text-sm md:text-base">
        Grab a pass and catch every performance across all venues &mdash; no per-show tickets needed.
    </p>

    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Day passes -->
        <?php foreach ($adDayPasses as $dayPass):
            $typeId    = (int) $dayPass->ticket_type_id;
            $price     = number_format((float) ($dayPass->ticket_scheme->price ?? 35), 0);
            $name      = htmlspecialchars($dayPass->ticket_scheme->name ?? 'Day Pass');
            $passDate  = $dayPass->schedule->date ?? $dayPass->schedule->start_time ?? null;
            $dayLabel  = $passDate ? $passDate->format('l') : $name;
            $dayShort  = $passDate ? $passDate->format('D j M') : '';
            $isSoldOut = !empty($dayPass->is_sold_out);
        ?>
        <li class="jazz_event_border_pink rounded-xl p-5 bg-white flex flex-col gap-3">
            <div>
                <h3 class="font-bold text-lg"><?= htmlspecialchars($dayLabel) ?> Pass</h3>
                <p class="text-gray-500 text-sm">All shows on <?= htmlspecialchars($dayLabel) ?> &mdash; all venues</p>
            </div>
            <data value="<?= $price ?>" class="text-3xl font-bold">&euro;<?= $price ?></data>
            <?php if ($isSoldOut): ?>
                <button type="button" disabled
                        class="w-full jazz_event_button_pink text-center opacity-50 cursor-not-allowed text-sm">
                    Sold Out
                </button>
            <?php else: ?>
                <button type="button"
                        data-action="buy-ticket"
                        data-ticket-type-id="<?= $typeId ?>"
                        data-artist="<?= $name ?>"
                        data-date="<?= htmlspecialchars($dayShort ?: $dayLabel) ?>"
                        data-venue="All Venues"
                        data-price="<?= $price ?>"
                        class="w-full jazz_event_button_pink text-center cursor-pointer text-sm">
                    Buy <?= htmlspecialchars($dayLabel) ?> Pass
                </button>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>

        <!-- Weekend / all-access pass -->
        <?php if ($adWeekendPass !== null):
            $wTypeId   = (int) $adWeekendPass->ticket_type_id;
            $wPrice    = number_format((float) ($adWeekendPass->ticket_scheme->price ?? 80), 0);
            $wName     = htmlspecialchars($adWeekendPass->ticket_scheme->name ?? 'Weekend Pass');
            $wSoldOut  = !empty($adWeekendPass->is_sold_out);
        ?>
        <li class="jazz_event_border_yellow rounded-xl p-5 bg-white flex flex-col gap-3 relative">
            <mark class="absolute top-3 right-3 jazz_event_bg_yellow text-gray-800 text-xs font-bold px-2 py-1 rounded-full">
                BEST VALUE
            </mark>

            <div>
                <h3 class="font-bold text-lg"><?= $wName ?></h3>
                <p class="text-gray-500 text-sm">All 4 days &mdash; every show, every venue</p>
            </div>
            <data value="<?= $wPrice ?>" class="text-3xl font-bold">&euro;<?= $wPrice ?></data>
            <?php if ($wSoldOut): ?>
                <button type="button" disabled
                        class="w-full jazz_event_button_yellow text-center opacity-50 cursor-not-allowed">
                    Sold Out
                </button>
            <?php else: ?>
                <button type="button"
                        data-action="buy-ticket"
                        data-ticket-type-id="<?= $wTypeId ?>"
                        data-artist="<?= $wName ?>"
                        data-date="Thu + Fri + Sat + Sun — All Indoor Shows"
                        data-venue="All Venues &amp; Stages"
                        data-price="<?= $wPrice ?>"
                        class="w-full jazz_event_button_yellow text-center cursor-pointer">
                    Buy <?= $wName ?>
                </button>
            <?php endif; ?>
        </li>
        <?php endif; ?>

    </ul>
</section>
