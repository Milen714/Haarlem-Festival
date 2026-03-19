<?php
namespace App\Views\Cms\Schedules\Tickets;

use App\Models\Schedule;
use App\Models\TicketType;

/** @var Schedule $schedule */
/** @var TicketType|null $ticketType */
$schedule = $schedule ?? null;
$ticketType = $ticketType ?? null;
$ticketSchemes = $ticketSchemes ?? [];
$isEdit = $ticketType !== null;
$pageTitle = $isEdit ? 'Edit Ticket Type #' . $ticketType->ticket_type_id : 'Create Ticket Type';
$action = $action ?? '';
?>

<section class="mx-auto max-w-4xl p-8">
    <header class="mb-8">
        <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Schedule Tickets</p>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="mb-2 text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>
                <p class="text-gray-600">
                    Schedule #<?= (int)$schedule->schedule_id ?>
                    on <?= htmlspecialchars($schedule->date?->format('d M Y') ?? 'Unknown date') ?>
                    at <?= htmlspecialchars($schedule->start_time?->format('H:i') ?? '--:--') ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="/cms/ticket-schemes"
                   class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50">
                    Manage Schemes
                </a>
                <a href="/cms/ticket-schemes/create"
                   class="rounded-lg bg-green-600 px-4 py-2 font-semibold text-white transition hover:bg-green-700">
                    Create Scheme
                </a>
            </div>
        </div>
    </header>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
            <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($action) ?>">
        <div class="mb-6 rounded-lg border bg-white p-6">
            <h2 class="mb-4 border-b pb-2 text-xl font-bold">Scheme &amp; Pricing</h2>

            <div class="mb-4">
                <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                    <label class="block font-semibold text-gray-700" for="scheme_id">
                        Ticket Scheme <span class="text-red-500">*</span>
                    </label>
                    <a href="/cms/ticket-schemes/create"
                       class="text-sm font-semibold text-green-700 transition hover:text-green-900">
                        + Create a new scheme
                    </a>
                </div>
                <select id="scheme_id" name="scheme_id" required
                        class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select ticket scheme —</option>
                    <?php foreach ($ticketSchemes as $ticketScheme): ?>
                        <option value="<?= (int)$ticketScheme->ticket_scheme_id ?>"
                            <?= (int)($ticketType?->ticket_scheme?->ticket_scheme_id ?? 0) === (int)$ticketScheme->ticket_scheme_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ticketScheme->name ?? 'Unnamed scheme') ?>
                            (<?= htmlspecialchars($ticketScheme->scheme_enum?->value ?? 'No enum') ?>)
                            - €<?= number_format((float)($ticketScheme->price ?? 0), 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="mb-2 block font-semibold text-gray-700" for="description">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                          placeholder="Optional label shown to admins or buyers"><?= htmlspecialchars($ticketType?->description ?? '') ?></textarea>
            </div>
        </div>

        <div class="mb-6 rounded-lg border bg-white p-6">
            <h2 class="mb-4 border-b pb-2 text-xl font-bold">Limits</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="min_quantity">
                        Minimum Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="min_quantity" name="min_quantity" min="1" required
                           value="<?= htmlspecialchars((string)($ticketType?->min_quantity ?? 1)) ?>"
                           class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="max_quantity">
                        Maximum Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="max_quantity" name="max_quantity" min="1" required
                           value="<?= htmlspecialchars((string)($ticketType?->max_quantity ?? 10)) ?>"
                           class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="capacity">
                        Capacity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacity" name="capacity" min="1" required
                           value="<?= htmlspecialchars((string)($ticketType?->capacity ?? $schedule->total_capacity ?? 1)) ?>"
                           class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="special_requirements">Special Requirements</label>
                    <input type="text" id="special_requirements" name="special_requirements"
                           value="<?= htmlspecialchars($ticketType?->special_requirements ?? '') ?>"
                           class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                           placeholder="Optional notes, accessibility info, dress code...">
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-lg border bg-white p-6">
            <h2 class="mb-4 border-b pb-2 text-xl font-bold">Age Restrictions</h2>

            <div>
                <label class="mb-2 block font-semibold text-gray-700" for="min_age">Minimum Age</label>
                <input type="number" id="min_age" name="min_age" min="0"
                       value="<?= htmlspecialchars((string)($ticketType?->min_age ?? '')) ?>"
                       class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                       placeholder="Leave empty if not applicable">
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white transition hover:bg-blue-700">
                <?= $isEdit ? 'Save Ticket Type' : 'Create Ticket Type' ?>
            </button>
            <a href="/cms/schedules/<?= (int)$schedule->schedule_id ?>/tickets"
               class="rounded-lg bg-gray-100 px-8 py-3 font-semibold text-gray-700 transition hover:bg-gray-200">
                Cancel
            </a>
        </div>
    </form>
</section>