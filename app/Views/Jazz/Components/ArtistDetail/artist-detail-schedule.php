<?php

namespace App\Views\Jazz\Components\ArtistDetail;

/**
 * Artist Detail Schedule — list of this artist's festival performances with buy buttons.
 *
 * @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm
 * @var \App\Models\MusicEvent\Artist                    $artist
 * @var string                                           $accentColor Color token for this artist (lavender/pink/coral/yellow).
 */
?>

<section aria-labelledby="artist-schedule-heading">
    <h2 id="artist-schedule-heading"
        class="text-2xl md:text-3xl font-bold mb-6"
        style="font-family: 'Cormorant Garamond', serif;">
        Performance Schedule
    </h2>

    <?php if (!empty($vm->scheduleByDate)): ?>
    <ul class="space-y-4">
        <?php foreach ($vm->scheduleByDate as $dateKey => $slots): ?>
        <?php foreach ($slots as $slot): ?>
        <?php
            $performanceDate    = $slot['date'];
            $startTime          = $slot['start_time'];
            $endTime            = $slot['end_time'];
            $isFreeEntry        = empty($slot['total_capacity']) || $slot['total_capacity'] === 0;
            $startTimeFormatted = ($startTime instanceof \DateTime) ? $startTime->format('H:i') : '';
            $endTimeFormatted   = ($endTime   instanceof \DateTime) ? $endTime->format('H:i')   : '';
        ?>
        <li class="jazz_event_border_<?= $accentColor ?> rounded-xl bg-white overflow-hidden">
            <article class="flex flex-col sm:flex-row items-stretch">

                <!-- Date badge -->
                <time datetime="<?= $performanceDate->format('Y-m-d') ?>"
                      class="jazz_event_bg_<?= $accentColor ?> flex sm:flex-col items-center sm:justify-center gap-2 sm:gap-0 px-4 sm:px-6 py-3 sm:py-4 text-center sm:min-w-[80px]">
                    <span class="text-2xl sm:text-3xl font-bold leading-none"><?= $performanceDate->format('j') ?></span>
                    <span class="text-sm font-semibold uppercase tracking-wide sm:mt-1"><?= $performanceDate->format('M') ?></span>
                </time>

                <!-- Performance details -->
                <section class="flex-1 px-4 sm:px-6 py-3 sm:py-4">
                    <h3 class="font-bold text-gray-900 text-base md:text-lg">
                        <?= htmlspecialchars($performanceDate->format('l') . ' — ' . ($slot['venue_name'] ?? '')) ?>
                    </h3>

                    <?php if (!empty($slot['venue_address'])): ?>
                        <p class="text-sm text-gray-500 mt-1">
                            <span aria-hidden="true">📍</span>
                            <?= htmlspecialchars($slot['venue_address']) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($startTime instanceof \DateTime): ?>
                        <p class="text-sm text-gray-600 mt-1">
                            <time datetime="<?= $startTimeFormatted ?>"><?= $startTimeFormatted ?></time>
                            <?php if ($endTime instanceof \DateTime): ?>
                                &ndash; <time datetime="<?= $endTimeFormatted ?>"><?= $endTimeFormatted ?></time>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($slot['venue_capacity'])): ?>
                        <p class="text-xs text-gray-400 mt-1">
                            <data value="<?= (int) $slot['venue_capacity'] ?>">
                                <?= htmlspecialchars($slot['venue_capacity']) ?>
                            </data> capacity
                        </p>
                    <?php endif; ?>
                </section>

                <!-- Ticket action -->
                <footer class="flex items-center px-4 sm:px-6 py-3 sm:py-4 border-t sm:border-t-0 sm:border-l border-gray-100">
                    <?php if ($slot['is_sold_out']): ?>
                        <span class="inline-block bg-gray-200 text-gray-500 text-sm font-bold px-4 py-2 rounded-lg w-full sm:w-auto text-center">
                            Sold Out
                        </span>
                    <?php elseif ($isFreeEntry): ?>
                        <span class="inline-block bg-green-100 text-green-700 text-sm font-bold px-4 py-2 rounded-lg w-full sm:w-auto text-center">
                            FREE Entry
                        </span>
                    <?php else: ?>
                        <button type="button"
                                data-action="buy-ticket"
                                data-schedule-id="<?= (int) ($slot['schedule_id'] ?? 0) ?>"
                                data-artist="<?= htmlspecialchars($artist->name ?? 'Artist TBA', ENT_QUOTES) ?>"
                                data-date="<?= htmlspecialchars($performanceDate->format('l, F j'), ENT_QUOTES) ?>"
                                data-start="<?= htmlspecialchars($startTimeFormatted, ENT_QUOTES) ?>"
                                data-end="<?= htmlspecialchars($endTimeFormatted, ENT_QUOTES) ?>"
                                data-venue="<?= htmlspecialchars($slot['venue_name'] ?? '', ENT_QUOTES) ?>"
                                data-price="<?= $slot['ticket_price'] !== null ? number_format((float) $slot['ticket_price'], 0) : '' ?>"
                                class="inline-block jazz_event_button_<?= $accentColor ?> text-sm font-bold px-4 py-2 rounded-lg w-full sm:w-auto text-center cursor-pointer">
                            Buy Tickets
                        </button>
                    <?php endif; ?>
                </footer>

            </article>
        </li>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>

    <?php else: ?>
        <p class="text-gray-400 italic">Schedule coming soon.</p>
    <?php endif; ?>
</section>
