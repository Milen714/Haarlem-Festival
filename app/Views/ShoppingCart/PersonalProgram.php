<?php
use App\ViewModels\ShoppingCart\OrderItemViewModel;

/** @var array $days */
/** @var array $items */
/** @var string $selectedDay */
$days        = $days ?? [];
$items       = $items ?? [];
$selectedDay = $selectedDay ?? '';
?>

<section class="container mx-auto max-w-3xl px-4 mt-10 mb-16 font-montserrat">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-[var(--text-home-primary)] uppercase tracking-wide">My Personal Program</h1>
        <div class="h-1 w-16 bg-[var(--home-gold-accent)] mt-2 rounded-full"></div>
    </div>

    <?php if (empty($days)): ?>

        <div class="bg-white border-t-4 border-[var(--home-gold-accent)] rounded-xl p-10 shadow-lg text-center">
            <p class="text-gray-500 font-semibold">Your program is empty.</p>
            <p class="text-gray-400 text-sm mt-1">Purchase tickets to build your personal schedule.</p>
        </div>

    <?php else: ?>

        <!-- Day Tabs -->
        <div class="flex gap-2 mb-6 flex-wrap">
            <?php foreach ($days as $dateKey => $day): ?>
                <a href="?date=<?= $dateKey ?>"
                   class="px-4 py-2 rounded-lg text-sm font-bold text-center transition-colors
                          <?= $dateKey === $selectedDay
                              ? 'bg-[var(--text-home-primary)] text-white'
                              : 'bg-white text-gray-500 border border-gray-200 hover:border-[var(--home-gold-accent)]' ?>">
                    <?= $day['tabLabel'] ?><br>
                    <span class="text-base"><?= $day['tabDay'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Ticket cards -->
        <div class="flex flex-col gap-3">
            <?php foreach ($items as $item):
                $p          = new OrderItemViewModel($item);
                $schedule   = $item->ticket_type->schedule;
                $cardStyles = $p->getCardStyles();
                $cardImage  = $p->getCardImage();
            ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                    <div class="px-4 pt-3">
                        <span class="text-xs font-bold uppercase tracking-widest px-2 py-0.5 rounded
                                     <?= $cardStyles['muted'] ?> text-[var(--text-home-primary)]">
                            HAARLEM <?= strtoupper($p->getEventLabel()) ?>
                        </span>
                    </div>

                    <div class="flex items-center gap-3 p-3">

                        <div class="flex-shrink-0 flex flex-col items-center justify-center
                                    <?= $cardStyles['muted'] ?> rounded-lg px-3 py-2 w-16 text-center">
                            <span class="text-xs font-bold text-[var(--text-home-primary)] uppercase">
                                <?= strtoupper($schedule->date?->format('D') ?? '') ?>
                            </span>
                            <span class="text-base font-black text-[var(--text-home-primary)]">
                                <?= $schedule->start_time?->format('H:i') ?? '' ?>
                            </span>
                        </div>

                        <div class="flex-grow min-w-0">
                            <h2 class="font-bold text-[var(--text-home-primary)] text-sm truncate">
                                <?= htmlspecialchars($p->getDisplayName()) ?>
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <?= $schedule->date?->format('Y-m-d') ?>
                                &nbsp;<?= $schedule->start_time?->format('H:i') ?> - <?= $schedule->end_time?->format('H:i') ?>
                            </p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($p->getVenueDisplay()) ?></p>
                            <p class="text-xs font-semibold text-gray-700 mt-0.5">x <?= (int)$item->quantity ?></p>
                        </div>

                        <div class="flex-shrink-0 font-bold text-[var(--text-home-primary)] text-sm">
                            &euro; <?= number_format((float)($item->subtotal ?? 0), 2) ?>
                        </div>

                        <?php if ($cardImage->file_path ?? null): ?>
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden">
                                <img src="<?= htmlspecialchars($cardImage->file_path) ?>"
                                     alt="<?= htmlspecialchars($cardImage->alt_text ?? '') ?>"
                                     class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100 bg-gray-50">
                        <a href="/my-tickets" class="text-xs font-bold text-[var(--text-home-primary)] hover:underline uppercase tracking-wide">
                            View Ticket
                        </a>
                        <span class="text-xs font-bold text-teal-700 bg-teal-100 px-3 py-0.5 rounded-full uppercase">
                            Owned
                        </span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <!-- Info notice -->
        <div class="mt-6 flex gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
            <span class="text-amber-500 font-bold text-lg leading-none">i</span>
            <div>
                <p class="text-sm font-bold text-amber-800">Your Program is saved automatically</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    Share with friends or convert saved Tickets to order when ready.<br>
                    Items in your Program are not confirmed until payment is completed.
                </p>
            </div>
        </div>

    <?php endif; ?>

</section>
