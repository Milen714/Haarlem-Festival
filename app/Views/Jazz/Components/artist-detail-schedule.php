<?php
/** @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm */
/** @var string $accentColor */
?>

<section aria-labelledby="schedule-heading">
    <h2 id="schedule-heading" class="text-3xl font-bold mb-6" style="font-family: 'Cormorant Garamond', serif;">
        Performance Schedule
    </h2>

    <?php if (!empty($vm->scheduleByDate)): ?>
    <ul class="space-y-4">
        <?php foreach ($vm->scheduleByDate as $dateKey => $slots): ?>
        <?php foreach ($slots as $slot): ?>

        <?php
            /** @var \DateTime $date */
            $date      = $slot['date'];
            $startTime = $slot['start_time']; // DateTime|null
            $endTime   = $slot['end_time'];   // DateTime|null
            $isFree    = ($slot['total_capacity'] === null || $slot['total_capacity'] === 0);
        ?>
        <li class="jazz_event_border_<?= $accentColor ?> rounded-xl bg-white overflow-hidden">
            <article class="flex items-stretch">

                <!-- Date block -->
                <time datetime="<?= $date->format('Y-m-d') ?>"
                      class="jazz_event_bg_<?= $accentColor ?> flex flex-col items-center justify-center px-6 py-4 text-center min-w-[80px]">
                    <span class="text-3xl font-bold leading-none"><?= $date->format('j') ?></span>
                    <span class="text-sm font-semibold uppercase tracking-wide mt-1"><?= $date->format('M') ?></span>
                </time>

                <!-- Details -->
                <section class="flex-1 px-6 py-4">
                    <h3 class="font-bold text-gray-900 text-lg">
                        <?= htmlspecialchars($date->format('l') . ' - ' . ($slot['venue_name'])) ?>
                    </h3>
                    <?php if (!empty($slot['venue_address'])): ?>
                    <p class="text-sm text-gray-500 mt-1">
                        📍 <?= htmlspecialchars($slot['venue_address']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($startTime instanceof \DateTime): ?>
                    <p class="text-sm text-gray-600 mt-1">
                        <time datetime="<?= $startTime->format('H:i') ?>"><?= $startTime->format('H:i') ?></time>
                        <?php if ($endTime instanceof \DateTime): ?>
                            – <time datetime="<?= $endTime->format('H:i') ?>"><?= $endTime->format('H:i') ?></time>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($slot['venue_capacity'])): ?>
                    <p class="text-xs text-gray-400 mt-1">
                        <data value="<?= (int)$slot['venue_capacity'] ?>"><?= htmlspecialchars($slot['venue_capacity']) ?></data> capacity
                    </p>
                    <?php endif; ?>
                </section>

                <!-- Ticket button -->
                <footer class="flex items-center px-6 py-4">
                    <?php if ($slot['is_sold_out']): ?>
                        <span class="inline-block bg-gray-200 text-gray-500 text-sm font-bold px-5 py-2 rounded-lg">
                            Sold Out
                        </span>
                    <?php elseif ($isFree): ?>
                        <span class="inline-block bg-green-100 text-green-700 text-sm font-bold px-5 py-2 rounded-lg">
                            FREE Entry
                        </span>
                    <?php else: ?>
                        <a href="/events-jazz#tickets"
                           class="inline-block jazz_event_button_<?= $accentColor ?> text-sm font-bold px-5 py-2 rounded-lg whitespace-nowrap">
                            Buy Tickets
                        </a>
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