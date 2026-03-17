<?php
namespace App\Views\Magic;

use App\ViewModels\Magic\MagicAccessibility;
use DateInterval;
use DatePeriod;
use DateTime;

/** @var MagicAccessibility $pageModel */
$heroSection = $pageModel->heroSection ?? null;

$calendarTitle = isset($calendarTitle) ? (string) $calendarTitle : 'July 2026';
$timeSlots = isset($timeSlots) && is_array($timeSlots) ? $timeSlots : [];
$supportUrl = isset($supportUrl) ? (string) $supportUrl : '/events-magic-accessibility';

if (empty($timeSlots) && !empty($pageModel->ticketsViewModel->schedulesByDate)) {
    $generatedSlots = [];
    $interval = new DateInterval('PT30M');

    foreach ($pageModel->ticketsViewModel->schedulesByDate as $schedule) {
        $startRaw = $schedule->start_time ?? null;
        $endRaw = $schedule->end_time ?? null;

        if ($startRaw === null || $endRaw === null) {
            continue;
        }

        $start = $startRaw instanceof DateTime ? clone $startRaw : new DateTime((string) $startRaw);
        $end = $endRaw instanceof DateTime ? clone $endRaw : new DateTime((string) $endRaw);

        if ($end < $start) {
            continue;
        }

        $period = new DatePeriod($start, $interval, (clone $end)->add($interval));

        foreach ($period as $time) {
            $generatedSlots[] = [
                'label' => $time->format('h:i A'),
                'scheduleId' => $schedule->schedule_id ?? null,
            ];
        }
    }

    $timeSlots = $generatedSlots;
}

?>

<section class="flex flex-col gap-6 bg_colors_home text-white pt-4 bg-[var(--magic-bg-primary)] overflow-x-hidden">
    <section class="w-[90%] mx-auto">
        <?php
        if ($pageModel->heroSection): 
            include 'Components/MagicAltHero.php';
            ?>
        <?php endif ?>
    </section>

    <section class="w-[90%] mx-auto">
        <?php include 'Components/MagicNav.php'; ?>
    </section>

    <section id="ticket-select-section"
        class="w-[90%] mx-auto mb-10 magic-border py-4 px-3 md:py-6 md:px-5 bg-[var(--magic-bg-secondary-dark)]">
        <section class="grid grid-cols-1 xl:grid-cols-[2fr_1fr] gap-6">
            <!-- Left column -->
            <section class="flex flex-col gap-6">
                <header>
                    <h2 class="font-courierprime text-xl md:text-2xl text-[var(--magic-gold-accent)]">Select a date and
                        time</h2>
                </header>

                <section
                    class="bg-[var(--magic-bg-primary)] rounded-md border border-[var(--magic-border-transperent-dark)] p-4 md:p-6">
                    <section class="flex items-center justify-between mb-4">
                        <button type="button" aria-label="Previous month"
                            class="font-robotomono text-xl text-[var(--magic-gold-accent)] hover:text-[var(--magic-bright-gold-accent)] transition-colors">
                            &lsaquo;
                        </button>
                        <h3 class="font-courierprime text-lg md:text-xl text-[var(--magic-gold-accent)]">
                            <?= htmlspecialchars($calendarTitle) ?></h3>
                        <button type="button" aria-label="Next month"
                            class="font-robotomono text-xl text-[var(--magic-gold-accent)] hover:text-[var(--magic-bright-gold-accent)] transition-colors">
                            &rsaquo;
                        </button>
                    </section>

                    <?php include __DIR__ . '/../Home/Components/Spinner.php'; ?>
                    <ul id="dates-ul" class="magicDayUl grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    </ul>
                </section>

                <section>
                    <h3 class="font-courierprime text-lg md:text-xl text-[var(--magic-gold-accent)] mb-3">Select a time
                        slot</h3>
                    <section class="bg-[var(--magic-bg-nav-muted)] rounded-md p-4 md:p-5">
                        <section id="time-slots" class="flex flex-wrap gap-3">
                            <?php foreach ($timeSlots as $index => $slot): ?>
                            <?php
                                $timeLabel = is_array($slot) ? (string) ($slot['label'] ?? '') : (string) $slot;
                                $scheduleId = is_array($slot) ? (string) ($slot['scheduleId'] ?? '') : '';
                                $isSelectedTime = is_array($slot) ? !empty($slot['isSelected']) : ($index === 0);
                            ?>
                            <button type="button" data-time-slot="<?= htmlspecialchars($timeLabel) ?>"
                                data-schedule-id="<?= htmlspecialchars($scheduleId) ?>"
                                class="h-10 px-3 rounded-md border text-xs md:text-sm font-robotomono transition-colors
                                    <?= $isSelectedTime
                                        ? 'bg-[#1b2949] border-[var(--magic-bright-gold-accent)] text-[var(--magic-bright-gold-accent)]'
                                        : 'bg-[#10233e] border-[var(--magic-creme-gold-accent)] text-[var(--magic-creme-gold-accent)] hover:bg-[#1a2f52]' ?>">
                                <?= htmlspecialchars($timeLabel) ?>
                            </button>
                            <?php endforeach; ?>
                        </section>
                    </section>
                </section>

                <button id="next-step-button" type="button"
                    class="w-full rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] py-3 font-courierprime text-[var(--magic-gold-accent)] text-lg hover:bg-[#11253f] transition-colors">
                    Next Step
                </button>
            </section>

            <!-- Right column -->
            <aside class="flex flex-col gap-4">
                <section
                    class="rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] p-5 flex-1">
                    <section class="mb-6">
                        <h3 class="font-courierprime text-2xl text-[var(--magic-gold-accent)] mb-3">Information</h3>
                        <p class="font-robotomono text-sm text-[#d5e0f0] leading-relaxed">
                            Book your tickets in advance, even if you have free admission (e.g. Netherlands Museum
                            Pass, Vriendenloterij VIP-Card). Purchased tickets will not be refunded. It is possible to
                            change the date and time of your tickets.
                        </p>
                    </section>

                    <section>
                        <h3 class="font-courierprime text-2xl text-[var(--magic-gold-accent)] mb-3">Frequently asked
                            questions</h3>
                        <p class="font-robotomono text-sm text-[#d5e0f0] leading-relaxed">
                            Refer to our
                            <a class="underline text-[var(--magic-blue-text)] hover:text-white"
                                href="/events-magic-accessibility">Frequently asked questions</a>
                            for quick answers about Teylers Museum, the ordering process, or changing your ticket.
                            If you need further assistance, our helpdesk is available to help you.
                        </p>
                    </section>
                </section>

                <a class="rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] py-3 px-4 text-center font-courierprime text-lg text-[var(--magic-gold-accent)] hover:bg-[#11253f] transition-colors"
                    href="<?= htmlspecialchars($supportUrl) ?>">
                    Contact Support
                </a>
            </aside>
        </section>
    </section>

    <section id="addToCartSection" class="w-[90%] mx-auto mb-10 hidden">
        <section class="magic-border py-4 px-3 md:py-6 md:px-5 bg-[var(--magic-bg-secondary-dark)]">
            <header class="mb-4">
                <h2 class="font-courierprime text-xl md:text-2xl text-[var(--magic-gold-accent)]">Choose your tickets
                </h2>
                <p id="ticket-type-status" class="font-robotomono text-sm text-[#d5e0f0] mt-2">Pick a time slot and
                    click Next Step.</p>
            </header>
            <ul id="ticket-type-list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></ul>
        </section>
    </section>

</section>



<script src="/Js/ScheduleDateButtons.js"></script>
<script>
displayDateButtons();
const spinner = document.getElementById('spinner');
const ticketSelectSection = document.getElementById('ticket-select-section');
const addToCartSection = document.getElementById('addToCartSection');
const nextStepButton = document.getElementById('next-step-button');

const datesUl = document.getElementById('dates-ul');
const timeSlotsContainer = document.getElementById('time-slots');
const ticketTypeList = document.getElementById('ticket-type-list');
const ticketTypeStatus = document.getElementById('ticket-type-status');

let selectedSlotButton = timeSlotsContainer.querySelector('button[data-schedule-id]');

function setSelectedSlot(button) {
    if (!button) {
        return;
    }

    const selectedClasses = ['bg-[#1b2949]', 'border-[var(--magic-bright-gold-accent)]',
        'text-[var(--magic-bright-gold-accent)]'
    ];
    const defaultClasses = ['bg-[#10233e]', 'border-[var(--magic-creme-gold-accent)]',
        'text-[var(--magic-creme-gold-accent)]', 'hover:bg-[#1a2f52]'
    ];

    if (selectedSlotButton) {
        selectedSlotButton.classList.remove(...selectedClasses);
        selectedSlotButton.classList.add(...defaultClasses);
    }

    button.classList.remove(...defaultClasses);
    button.classList.add(...selectedClasses);
    selectedSlotButton = button;
}

if (selectedSlotButton) {
    setSelectedSlot(selectedSlotButton);
}

timeSlotsContainer.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-schedule-id]');
    if (!button) {
        return;
    }
    setSelectedSlot(button);
});

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function renderTicketTypes(ticketTypes) {
    ticketTypeList.innerHTML = '';

    if (!Array.isArray(ticketTypes) || ticketTypes.length === 0) {
        ticketTypeStatus.textContent = 'No ticket types found for this schedule.';
        return;
    }

    ticketTypes.forEach((ticketType) => {
        const ticketTypeId = Number(ticketType.ticket_type_id ?? 0);
        const ticketName = ticketType.ticket_scheme?.name ?? 'Ticket';
        const price = Number(ticketType.ticket_scheme?.price ?? 0);
        const minQuantity = Number(ticketType.min_quantity ?? 1);
        const maxQuantity = Number(ticketType.max_quantity ?? 10);
        const description = ticketType.description ?? '';

        const item = document.createElement('li');
        item.className =
            'rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-primary)] p-4';
        item.innerHTML = `
            <section class="ticket-type-card flex flex-col gap-4" data-ticket-type-id="${escapeHtml(ticketTypeId)}">
                <section>
                    <h3 class="font-courierprime text-lg text-[var(--magic-gold-accent)]">${escapeHtml(ticketName)}</h3>
                    <p class="font-robotomono text-sm text-[#d5e0f0]">EUR ${escapeHtml(price.toFixed(2))}</p>
                    ${description ? `<p class="font-robotomono text-xs text-[#d5e0f0] mt-2">${escapeHtml(description)}</p>` : ''}
                </section>
                <section>
                    <p class="font-robotomono text-sm text-[var(--magic-creme-gold-accent)] mb-2">Quantity</p>
                    <section class="flex items-center gap-2">
                        <button type="button" class="qty-decrease h-10 w-10 rounded-md border border-[var(--magic-creme-gold-accent)] bg-[#10233e] font-courierprime text-[var(--magic-creme-gold-accent)]" data-min="${escapeHtml(minQuantity)}">-</button>
                        <input
                            id="qty-${escapeHtml(ticketTypeId)}"
                            type="number"
                            min="${escapeHtml(minQuantity)}"
                            max="${escapeHtml(maxQuantity)}"
                            value="${escapeHtml(minQuantity)}"
                            class="ticket-quantity w-24 rounded-md border border-[var(--magic-creme-gold-accent)] bg-[#10233e] px-3 py-2 text-center font-robotomono text-[var(--magic-creme-gold-accent)]"
                            required
                        >
                        <button type="button" class="qty-increase h-10 w-10 rounded-md border border-[var(--magic-creme-gold-accent)] bg-[#10233e] font-courierprime text-[var(--magic-creme-gold-accent)]" data-max="${escapeHtml(maxQuantity)}">+</button>
                    </section>
                    <p class="font-robotomono text-xs text-[#d5e0f0] mt-2">Min ${escapeHtml(minQuantity)} - Max ${escapeHtml(maxQuantity)}</p>
                </section>
                <button type="button" class="add-to-cart-button rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] py-2 px-3 font-courierprime text-[var(--magic-gold-accent)] hover:bg-[#11253f] transition-colors">
                    Add to cart
                </button>
            </section>
        `;
        ticketTypeList.appendChild(item);
    });
}

nextStepButton.addEventListener('click', async () => {
    const scheduleId = selectedSlotButton?.dataset.scheduleId;

    if (!scheduleId) {
        ticketTypeStatus.textContent = 'Please select a time slot first.';
        addToCartSection.classList.remove('hidden');
        return;
    }

    ticketTypeStatus.textContent = 'Loading ticket types...';
    ticketTypeList.innerHTML = '';
    addToCartSection.classList.remove('hidden');

    try {
        const response = await fetch(`/magic-get-ticketypes?schedule_id=${encodeURIComponent(scheduleId)}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to load ticket types.');
        }

        ticketTypeStatus.textContent = 'Select quantity and add tickets to cart.';
        renderTicketTypes(result.data);
        ticketSelectSection.classList.add('hidden');
    } catch (error) {
        ticketTypeStatus.textContent = error.message || 'Something went wrong while loading ticket types.';
    }
});

ticketTypeList.addEventListener('click', async (event) => {
    const card = event.target.closest('.ticket-type-card');
    if (!card) {
        return;
    }

    const quantityInput = card.querySelector('.ticket-quantity');
    if (!quantityInput) {
        return;
    }

    const min = Number(quantityInput.min || 1);
    const max = Number(quantityInput.max || 10);
    const currentValue = Number(quantityInput.value || min);

    if (event.target.closest('.qty-decrease')) {
        quantityInput.value = String(Math.max(min, currentValue - 1));
        return;
    }

    if (event.target.closest('.qty-increase')) {
        quantityInput.value = String(Math.min(max, currentValue + 1));
        return;
    }

    if (!event.target.closest('.add-to-cart-button')) {
        return;
    }

    const clampedQuantity = Math.min(max, Math.max(min, Number(quantityInput.value || min)));
    quantityInput.value = String(clampedQuantity);

    const payload = {
        ticketTypeId: Number(card.dataset.ticketTypeId || 0),
        quantity: clampedQuantity,
    };

    try {
        const response = await fetch('/addToCart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Could not add ticket to cart.');
        }

        ticketTypeStatus.textContent = 'Ticket added to cart.';
    } catch (error) {
        ticketTypeStatus.textContent = error.message || 'Could not add ticket to cart.';
    }
});
</script>