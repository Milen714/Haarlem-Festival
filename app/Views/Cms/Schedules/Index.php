<?php

namespace App\Views\Cms\Schedules;

use App\Models\Schedule;

/** @var Schedule[] $schedules */
$schedules       = $schedules ?? [];
$eventCategories = $eventCategories ?? [];
$filterType      = $filterType ?? '';
$filterDate      = $filterDate ?? '';
?>

<section class="mx-auto max-w-7xl p-4 md:p-8">
    <!-- Header -->
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="mb-2 text-3xl font-bold md:text-4xl" style="font-family: 'Cormorant Garamond', serif;">
                Manage Schedules
            </h1>
            <p class="text-gray-600">All event time slots</p>
        </div>
        <a href="/cms/schedules/create"
            class="w-full rounded-lg bg-green-600 px-6 py-3 text-center font-semibold text-white transition hover:bg-green-700 md:w-auto">
            + Add New Schedule
        </a>
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

    <?php if (isset($error)): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <p class="font-medium">✗ <?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="GET" action="/cms/schedules"
        class="mb-6 grid grid-cols-1 gap-3 rounded-lg border bg-white p-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end lg:gap-4">
        <div class="w-full lg:w-auto">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Event Type</label>
            <select name="event_type" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="">All types</option>
                <?php foreach ($eventCategories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['type']) ?>"
                        <?= $filterType === $cat['type'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full lg:w-auto">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>"
                class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700 lg:w-auto">
            Filter
        </button>
        <?php if ($filterType || $filterDate): ?>
            <a href="/cms/schedules?clear=1"
                class="w-full rounded-lg border px-4 py-2 text-center text-gray-500 transition hover:text-gray-700 lg:w-auto">
                Clear
            </a>
        <?php endif; ?>
    </form>

    <!-- Table -->
    <?php if (empty($schedules)): ?>
        <div class="bg-white border rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">📅</div>
            <h3 class="text-2xl font-bold mb-2">No Schedules Found</h3>
            <p class="text-gray-600 mb-6">Start by adding your first schedule</p>
            <a href="/cms/schedules/create"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First Schedule
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4 xl:hidden">
            <?php foreach ($schedules as $schedule): ?>
                <?php
                // Aggregate tickets_sold and is_sold_out from ticket types
                $sold = 0;
                $isSoldOut = false;
                if (isset($schedule->ticketTypes) && is_array($schedule->ticketTypes)) {
                    foreach ($schedule->ticketTypes as $tt) {
                        $sold += $tt->tickets_sold ?? 0;
                        if (!empty($tt->is_sold_out)) {
                            $isSoldOut = true;
                        }
                    }
                }
                $total = $schedule->total_capacity ?? 0;
                $pct = $total > 0 ? round(($sold / $total) * 100) : 0;
                ?>
                <article class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm text-gray-500">Schedule #<?= (int)$schedule->schedule_id ?></p>
                            <p class="font-semibold text-gray-900">
                                <?= $schedule->date ? htmlspecialchars($schedule->date->format('d M Y')) : '—' ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <?= $schedule->start_time ? $schedule->start_time->format('H:i') : '—' ?>
                                -
                                <?= $schedule->end_time ? $schedule->end_time->format('H:i') : '—' ?>
                            </p>
                        </div>
                        <?php if ($schedule->event_category): ?>
                            <span class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                <?= htmlspecialchars($schedule->event_category->type->value) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-1 text-sm text-gray-700">
                        <p><span class="font-semibold">Venue:</span>
                            <?= $schedule->venue ? htmlspecialchars($schedule->venue->name) : '—' ?></p>
                        <p>
                            <span class="font-semibold">Linked:</span>
                            <?php if ($schedule->artist): ?>
                                🎵 <?= htmlspecialchars($schedule->artist->name) ?>
                            <?php elseif ($schedule->restaurant): ?>
                                🍽️ <?= htmlspecialchars($schedule->restaurant->name) ?>
                            <?php elseif ($schedule->landmark): ?>
                                🏛️ <?= htmlspecialchars($schedule->landmark->name) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </p>
                        <p>
                            <span class="font-semibold">Capacity:</span>
                            <?= (int)$sold ?> / <?= (int)$total ?>
                            <?php if ($isSoldOut): ?>
                                <span
                                    class="ml-1 inline-block bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded">SOLD
                                    OUT</span>
                            <?php elseif ($pct >= 80): ?>
                                <span
                                    class="ml-1 inline-block rounded bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700"><?= $pct ?>%</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets"
                            class="rounded-lg bg-amber-100 px-3 py-2 text-center font-semibold text-amber-800 transition hover:bg-amber-200">
                            Manage Tickets
                        </a>
                        <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets/create"
                            class="rounded-lg bg-green-100 px-3 py-2 text-center font-semibold text-green-800 transition hover:bg-green-200">
                            Add Ticket
                        </a>
                        <a href="/cms/schedules/edit/<?= $schedule->schedule_id ?>"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white transition hover:bg-blue-700">
                            Edit
                        </a>
                        <form method="POST" action="/cms/schedules/delete/<?= $schedule->schedule_id ?>"
                            onsubmit="return confirm('Delete this schedule? This cannot be undone.')">
                            <button type="submit"
                                class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="hidden overflow-x-auto rounded-lg border bg-white shadow-sm xl:block">
            <table class="w-full min-w-[1150px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">#</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Event Type</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Date</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Time</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Venue</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Linked To</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Capacity</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Tickets</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($schedules as $schedule): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500 text-sm"><?= $schedule->schedule_id ?></td>

                            <!-- Event Type -->
                            <td class="px-6 py-4">
                                <?php if ($schedule->event_category): ?>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                        <?= htmlspecialchars($schedule->event_category->type->value) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4">
                                <?= $schedule->date ? htmlspecialchars($schedule->date->format('d M Y')) : '—' ?>
                            </td>

                            <!-- Time -->
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= $schedule->start_time ? $schedule->start_time->format('H:i') : '—' ?>
                                –
                                <?= $schedule->end_time ? $schedule->end_time->format('H:i') : '—' ?>
                            </td>

                            <!-- Venue -->
                            <td class="px-6 py-4 text-sm">
                                <?= $schedule->venue ? htmlspecialchars($schedule->venue->name) : '<span class="text-gray-400">—</span>' ?>
                            </td>

                            <!-- Linked To (Artist / Restaurant / Landmark) -->
                            <td class="px-6 py-4 text-sm">
                                <?php if ($schedule->artist): ?>
                                    <span class="text-purple-700">🎵 <?= htmlspecialchars($schedule->artist->name) ?></span>
                                <?php elseif ($schedule->restaurant): ?>
                                    <span class="text-orange-700">🍽️ <?= htmlspecialchars($schedule->restaurant->name) ?></span>
                                <?php elseif ($schedule->landmark): ?>
                                    <span class="text-green-700">🏛️ <?= htmlspecialchars($schedule->landmark->name) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Capacity -->
                            <td class="px-6 py-4 text-sm">
                                <?php
                                // TicketType aggregation or placeholder (integration pending)
                                $sold = 0;
                                $isSoldOut = false;
                                if (isset($schedule->ticketTypes) && is_array($schedule->ticketTypes)) {
                                    foreach ($schedule->ticketTypes as $tt) {
                                        $sold += $tt->tickets_sold ?? 0;
                                        if (!empty($tt->is_sold_out)) {
                                            $isSoldOut = true;
                                        }
                                    }
                                }
                                $total = $schedule->total_capacity ?? 0;
                                $pct = $total > 0 ? round(($sold / $total) * 100) : 0;
                                ?>
                                <div>
                                    <?= $sold ?> / <?= $total ?>
                                    <?php if ($isSoldOut): ?>
                                        <span
                                            class="ml-1 inline-block bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded">SOLD
                                            OUT</span>
                                    <?php elseif ($pct >= 80): ?>
                                        <span
                                            class="ml-1 inline-block bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded"><?= $pct ?>%</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets"
                                        class="inline-flex items-center rounded-lg bg-amber-100 px-3 py-1.5 font-semibold text-amber-800 transition hover:bg-amber-200">
                                        Manage Tickets
                                    </a>
                                    <a href="/cms/schedules/<?= $schedule->schedule_id ?>/tickets/create"
                                        class="inline-flex items-center rounded-lg bg-green-100 px-3 py-1.5 font-semibold text-green-800 transition hover:bg-green-200">
                                        Add Ticket
                                    </a>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/cms/schedules/edit/<?= $schedule->schedule_id ?>"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="/cms/schedules/delete/<?= $schedule->schedule_id ?>"
                                        onsubmit="return confirm('Delete this schedule? This cannot be undone.')">
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-sm text-gray-500">Showing <?= count($schedules) ?> schedule(s)</p>
    <?php endif; ?>
</section>