<?php
namespace App\Views\Cms\Schedules\Tickets;

use App\Models\Schedule;

/** @var Schedule $schedule */
$schedule = $schedule ?? null;
$ticketTypes = $ticketTypes ?? [];
?>

<section class="mx-auto max-w-7xl p-8">
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Schedule Tickets</p>
            <h1 class="mb-2 text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                Manage Tickets for Schedule #<?= (int)$schedule->schedule_id ?>
            </h1>
            <p class="text-gray-600">
                <?= htmlspecialchars($schedule->event_category?->title ?? $schedule->event_category?->type?->value ?? 'Schedule') ?>
                on <?= htmlspecialchars($schedule->date?->format('d M Y') ?? 'Unknown date') ?>
                from <?= htmlspecialchars($schedule->start_time?->format('H:i') ?? '--:--') ?>
                to <?= htmlspecialchars($schedule->end_time?->format('H:i') ?? '--:--') ?>
            </p>
        </div>

        <div class="flex gap-3">
            <a href="/cms/schedules?event_type=<?= urlencode($schedule->event_category?->type?->value ?? '') ?>"
               class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50">
                Back to Schedules
            </a>
            <a href="/cms/schedules/<?= (int)$schedule->schedule_id ?>/tickets/create"
               class="rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white transition hover:bg-green-700">
                + Add Ticket Type
            </a>
        </div>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 rounded border-l-4 border-green-500 bg-green-100 p-4 text-green-700">
            <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
            <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($ticketTypes)): ?>
        <div class="rounded-lg border bg-white p-10 text-center shadow-sm">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">No Ticket Types Yet</h2>
            <p class="mb-6 text-gray-600">Add your first ticket option for this schedule slot.</p>
            <a href="/cms/schedules/<?= (int)$schedule->schedule_id ?>/tickets/create"
               class="inline-flex rounded-lg bg-amber-500 px-5 py-3 font-semibold text-white transition hover:bg-amber-600">
                Create Ticket Type
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <table class="w-full">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Scheme</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Capacity</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Age</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($ticketTypes as $ticketType): ?>
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    <?= htmlspecialchars($ticketType->ticket_scheme->name ?? 'Unnamed scheme') ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= htmlspecialchars($ticketType->ticket_scheme->scheme_enum?->value ?? 'No enum') ?>
                                    <?php if ($ticketType->ticket_scheme->ticket_language): ?>
                                        • <?= htmlspecialchars($ticketType->ticket_scheme->ticket_language->value) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= htmlspecialchars($ticketType->description ?? 'No description') ?>
                                <?php if ($ticketType->special_requirements): ?>
                                    <div class="mt-1 text-xs text-amber-700">
                                        Requirements: <?= htmlspecialchars($ticketType->special_requirements) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>€<?= number_format((float)($ticketType->ticket_scheme->price ?? 0), 2) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= (int)($ticketType->min_quantity ?? 0) ?> - <?= (int)($ticketType->max_quantity ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= (int)($ticketType->capacity ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if ($ticketType->min_age !== null || $ticketType->max_age !== null): ?>
                                    <?= htmlspecialchars((string)($ticketType->min_age ?? 0)) ?> - <?= htmlspecialchars((string)($ticketType->max_age ?? 99)) ?>
                                <?php else: ?>
                                    All ages
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="/cms/schedules/<?= (int)$schedule->schedule_id ?>/tickets/edit/<?= (int)$ticketType->ticket_type_id ?>"
                                       class="rounded bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="/cms/schedules/<?= (int)$schedule->schedule_id ?>/tickets/delete/<?= (int)$ticketType->ticket_type_id ?>"
                                          onsubmit="return confirm('Delete this ticket type? This cannot be undone.')">
                                        <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-red-700">
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
    <?php endif; ?>
</section>