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
                <button data-date="<?= $dateKey ?>"
                        onclick="switchDay(this)"
                        class="day-tab px-4 py-2 rounded-lg text-sm font-bold text-center transition-colors
                               <?= $dateKey === $selectedDay
                                   ? 'bg-[var(--text-home-primary)] text-white'
                                   : 'bg-white text-gray-500 border border-gray-200 hover:border-[var(--home-gold-accent)]' ?>">
                    <?= $day['tabLabel'] ?><br>
                    <span class="text-base"><?= $day['tabDay'] ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Ticket cards -->
        <div class="flex flex-col gap-3">
            <?php foreach ($items as $item):
                $p          = new OrderItemViewModel($item);
                $schedule   = $item->ticket_type->schedule;
                $cardStyles = $p->getCardStyles();
                $cardImage  = $p->getCardImage();
                $dateKey    = $schedule->date?->format('Y-m-d') ?? 'unknown';
            ?>
                <div class="program-item"
                     data-date="<?= $dateKey ?>"
                     <?= $dateKey !== $selectedDay ? 'style="display:none"' : '' ?>>

                    <article class="flex flex-row w-full rounded-lg overflow-hidden shadow-md border border-gray-200">
                        <div class="relative calendar-coils w-2 md:w-[0.65rem] <?= $cardStyles['side'] ?> text-transparent flex-shrink-0">
                            hh
                            <div class="absolute w-[1rem] h-[1rem] -bottom-[-2rem] -left-2.5 bg_colors_home rounded-full"></div>
                        </div>

                        <img loading="lazy" src="<?= htmlspecialchars($cardImage->file_path ?? '') ?>"
                             alt="<?= htmlspecialchars($cardImage->alt_text ?? '') ?>"
                             class="w-16 sm:w-20 md:w-30 lg:w-40 h-auto object-cover flex-shrink-0">

                        <div class="flex flex-col flex-grow min-w-0">
                            <div class="flex flex-col py-1">
                                <div class="flex flex-col lg:flex-row justify-between gap-3 mt-2">
                                    <div class="flex pl-2 gap-2 items-center">
                                        <div class="h-min px-2 md:px-4 py-2 md:py-4 <?= $cardStyles['muted'] ?> rounded-md flex-shrink-0">
                                            <h1 class="font-bold text-lg md:text-2xl text-black text-center">
                                                <?= htmlspecialchars($p->getDateBoxLabel()) ?>
                                            </h1>
                                        </div>
                                        <div class="flex flex-col">
                                            <h2 class="text-black text-sm md:text-lg font-semibold">
                                                <?= htmlspecialchars($p->getDisplayName()) ?>
                                            </h2>
                                            <div class="flex flex-col gap-2">
                                                <div class="flex flex-row gap-2 items-center">
                                                    <img src="/Assets/Home/LocationTicketHome.svg" alt="Location Icon" class="w-4 h-4 flex-shrink-0">
                                                    <span class="text-sm text-black"><?= htmlspecialchars($p->getVenueDisplay()) ?></span>
                                                </div>
                                                <div class="flex flex-row gap-2 items-center">
                                                    <img src="/Assets/Home/DurationIconHome.svg" alt="Duration Icon" class="w-4 h-4 flex-shrink-0">
                                                    <span class="text-sm font-bold text-black whitespace-nowrap"><?= htmlspecialchars($p->getDurationDisplay()) ?></span>
                                                </div>
                                                <div class="flex flex-row gap-2 items-center">
                                                    <img src="/Assets/Home/PersonIcon.svg" alt="Quantity Icon" class="w-4 h-4 flex-shrink-0">
                                                    <span class="text-sm font-bold text-black whitespace-nowrap">x <?= (int)$item->quantity ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <article class="flex-shrink-0 flex items-center mr-3 justify-center">
                                        <h3 class="font-semibold text-xs md:text-base mb-2 text-black">
                                            €<?= number_format((float)($item->subtotal ?? 0), 2) ?>
                                        </h3>
                                    </article>
                                </div>
                            </div>

                            <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100 bg-[var(--text-home-primary)]">
                                <a href="/my-tickets" class="text-xs font-bold text-white hover:underline uppercase tracking-wide">
                                    View Ticket
                                </a>
                                
                        </div>
                    </article>

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

<script>
function switchDay(btn) {
    const date = btn.dataset.date;

    document.querySelectorAll('.day-tab').forEach(t => {
        t.className = t.className
            .replace('bg-[var(--text-home-primary)] text-white', '')
            + ' bg-white text-gray-500 border border-gray-200';
    });
    btn.className = btn.className
        .replace('bg-white text-gray-500 border border-gray-200', '')
        + ' bg-[var(--text-home-primary)] text-white';

    document.querySelectorAll('.program-item').forEach(el => {
        el.style.display = el.dataset.date === date ? '' : 'none';
    });
}
</script>
