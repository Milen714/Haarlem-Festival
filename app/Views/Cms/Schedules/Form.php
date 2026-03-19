<?php

namespace App\Views\Cms\Schedules;

use App\Models\Schedule;
use App\Models\TicketType;

/** @var Schedule|null $schedule */
$schedule        = $schedule ?? null;
$isEdit          = $schedule !== null;
$pageTitle       = $isEdit ? 'Edit Schedule #' . $schedule->schedule_id : 'Create New Schedule';
$action          = $action ?? '/cms/schedules/store';
$eventCategories = $eventCategories ?? [];
$venues          = $venues ?? [];
$artists         = $artists ?? [];
$restaurants     = $restaurants ?? [];
$landmarks       = $landmarks ?? [];
$ticketTypes     = $ticketTypes ?? [];
?>

<section class="mx-auto max-w-4xl p-4 md:p-8">
    <!-- Header -->
    <header class="mb-8">
        <h1 class="mb-2 text-3xl font-bold md:text-4xl" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update this schedule slot' : 'Add a new schedule slot to the festival' ?>
        </p>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="<?= htmlspecialchars($action) ?>">

        <!-- Event & Venue -->
        <div class="mb-6 rounded-lg border bg-white p-4 md:p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Event Details</h2>

            <!-- Event Category -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="event_id">
                    Event Category <span class="text-red-500">*</span>
                </label>
                <select id="event_id" name="event_id" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— Select event category —</option>
                    <?php foreach ($eventCategories as $cat): ?>
                        <option value="<?= (int)$cat['event_id'] ?>"
                            data-type="<?= htmlspecialchars($cat['type']) ?>"
                            <?= (int)($schedule->event_id ?? 0) === (int)$cat['event_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['title']) ?> (<?= htmlspecialchars($cat['type']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Venue -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="venue_id">
                    Venue
                </label>
                <select id="venue_id" name="venue_id"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— No venue —</option>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?= (int)$venue->venue_id ?>"
                            <?= (int)($schedule->venue_id ?? 0) === (int)$venue->venue_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($venue->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Date & Time -->
        <div class="mb-6 rounded-lg border bg-white p-4 md:p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Date &amp; Time</h2>

            <!-- Date -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="date">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date" name="date" required
                    value="<?= htmlspecialchars($schedule?->date?->format('Y-m-d') ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Start Time -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="start_time">
                        Start Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time" required
                        value="<?= htmlspecialchars($schedule?->start_time?->format('H:i') ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="end_time">
                        End Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="end_time" name="end_time" required
                        value="<?= htmlspecialchars($schedule?->end_time?->format('H:i') ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Capacity -->
        <div class="mb-6 rounded-lg border bg-white p-4 md:p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Capacity</h2>

            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Total Capacity -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="total_capacity">
                        Total Capacity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="total_capacity" name="total_capacity" min="1" required
                        value="<?= htmlspecialchars((string)($schedule?->total_capacity ?? '')) ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. 200">
                </div>

                <!-- Tickets Sold -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="tickets_sold">
                        Tickets Sold
                    </label>
                    <input type="number" id="tickets_sold" name="tickets_sold" min="0"
                        value="<?= htmlspecialchars((string)($schedule?->tickets_sold ?? '0')) ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Sold Out -->
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_sold_out" value="0">
                <input type="checkbox" id="is_sold_out" name="is_sold_out" value="1"
                    <?= ($schedule?->is_sold_out ?? false) ? 'checked' : '' ?>
                    class="w-4 h-4 text-red-600 border-gray-300 rounded">
                <label for="is_sold_out" class="text-gray-700 font-semibold">Mark as Sold Out</label>
            </div>
        </div>

        <!-- Optional Links -->
        <div class="mb-6 rounded-lg border bg-white p-4 md:p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Link to (optional)</h2>
            <p class="text-sm text-gray-500 mb-4">Link this schedule slot to the relevant entity for the selected event type.</p>

            <!-- Artist -->
            <div class="mb-4" data-link-type="artist" style="display:none;">
                <label class="block text-gray-700 font-semibold mb-2" for="artist_id">Artist</label>
                <select id="artist_id" name="artist_id"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— None —</option>
                    <?php foreach ($artists as $artist): ?>
                        <option value="<?= (int)$artist->artist_id ?>"
                            <?= (int)($schedule?->artist_id ?? 0) === (int)$artist->artist_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($artist->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Restaurant -->
            <div class="mb-4" data-link-type="restaurant" style="display:none;">
                <label class="block text-gray-700 font-semibold mb-2" for="restaurant_id">Restaurant</label>
                <select id="restaurant_id" name="restaurant_id"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— None —</option>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <option value="<?= (int)$restaurant->restaurant_id ?>"
                            <?= (int)($schedule?->restaurant_id ?? 0) === (int)$restaurant->restaurant_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($restaurant->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Landmark -->
            <div class="mb-4" data-link-type="landmark" style="display:none;">
                <label class="block text-gray-700 font-semibold mb-2" for="landmark_id">Landmark</label>
                <select id="landmark_id" name="landmark_id"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— None —</option>
                    <?php foreach ($landmarks as $landmark): ?>
                        <option value="<?= (int)$landmark->landmark_id ?>"
                            <?= (int)($schedule?->landmark_id ?? 0) === (int)$landmark->landmark_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($landmark->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p id="link-no-entity" class="text-sm text-gray-400 italic" style="display:none;">No linked entity for this event type.</p>
        </div>

        <script>
        (function () {
            const typeLinkMap = {
                'Jazz':    'artist',
                'Dance':   'artist',
                'Yummy':   'restaurant',
                'History': 'landmark',
                'Magic':   null,
            };

            const eventSelect = document.getElementById('event_id');
            const linkDivs    = document.querySelectorAll('[data-link-type]');
            const noEntityMsg = document.getElementById('link-no-entity');

            function updateLinks() {
                const selected = eventSelect.options[eventSelect.selectedIndex];
                const type     = selected ? selected.dataset.type : null;
                const linkType = typeLinkMap[type] ?? null;

                linkDivs.forEach(function (div) {
                    const match = div.dataset.linkType === linkType;
                    div.style.display = match ? '' : 'none';
                    if (!match) {
                        const sel = div.querySelector('select');
                        if (sel) sel.value = '';
                    }
                });

                if (noEntityMsg) {
                    noEntityMsg.style.display = (type && linkType === null) ? '' : 'none';
                }
            }

            eventSelect.addEventListener('change', updateLinks);
            updateLinks();
        })();
        </script>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <div class="mb-4 flex flex-col items-start justify-between gap-4 border-b pb-3 md:flex-row">
                <div>
                    <h2 class="text-xl font-bold">Tickets</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        <?= $isEdit ? 'Manage ticket types for this schedule slot.' : 'Create the schedule first, then you can add ticket types.' ?>
                    </p>
                </div>

                <?php if ($isEdit): ?>
                    <div class="flex w-full flex-wrap gap-3 md:w-auto">
                        <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets/create"
                            class="w-full rounded-lg bg-green-600 px-4 py-2 text-center font-semibold text-white transition hover:bg-green-700 sm:w-auto">
                            Add Ticket Type
                        </a>
                        <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets"
                            class="w-full rounded-lg bg-amber-500 px-4 py-2 text-center font-semibold text-white transition hover:bg-amber-600 sm:w-auto">
                            Manage Tickets
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$isEdit): ?>
                <p class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    Ticket types are attached to an existing schedule, so this section becomes available after the schedule
                    is saved.
                </p>
            <?php elseif (empty($ticketTypes)): ?>
                <div
                    class="flex flex-wrap items-center justify-between gap-4 rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-4">
                    <p class="text-sm text-gray-600">
                        No ticket types have been added for this schedule yet.
                    </p>
                    <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets/create"
                        class="w-full rounded-lg bg-green-600 px-4 py-2 text-center font-semibold text-white transition hover:bg-green-700 sm:w-auto">
                        Create First Ticket Type
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-lg border">
                    <table class="w-full min-w-[620px] text-sm">
                        <thead class="bg-gray-50 text-left text-gray-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Scheme</th>
                                <th class="px-4 py-3 font-semibold">Price</th>
                                <th class="px-4 py-3 font-semibold">Capacity</th>
                                <th class="px-4 py-3 font-semibold">Rules</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($ticketTypes as $ticketType): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">
                                            <?= htmlspecialchars($ticketType->ticket_scheme->name ?? 'Unnamed scheme') ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= htmlspecialchars($ticketType->ticket_scheme->scheme_enum?->value ?? 'No enum') ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        €<?= number_format((float)($ticketType->ticket_scheme->price ?? 0), 2) ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <?= (int)($ticketType->capacity ?? 0) ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <?= (int)($ticketType->min_quantity ?? 0) ?>-<?= (int)($ticketType->max_quantity ?? 0) ?>
                                        per order
                                        <?php if ($ticketType->min_age !== null || $ticketType->max_age !== null): ?>
                                            <div class="text-xs text-gray-500">
                                                Age: <?= htmlspecialchars((string)($ticketType->min_age ?? 0)) ?> -
                                                <?= htmlspecialchars((string)($ticketType->max_age ?? 99)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
            <button type="submit"
                class="w-full rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                <?= $isEdit ? 'Save Changes' : 'Create Schedule' ?>
            </button>
            <a href="/cms/schedules"
                class="w-full rounded-lg bg-gray-100 px-8 py-3 text-center font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto">
                Cancel
            </a>
        </div>
    </form>
</section>