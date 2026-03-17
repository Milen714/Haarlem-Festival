<?php
namespace App\Views\Cms\TicketSchemes;

use App\Models\Enums\TicketLanguageEnum;
use App\Models\Enums\TicketSchemeEnum;
use App\Models\TicketScheme;

/** @var TicketScheme|null $ticketScheme */
$ticketScheme = $ticketScheme ?? null;
$action = $action ?? '/cms/ticket-schemes/store';
$isEdit = $ticketScheme !== null;
$pageTitle = $isEdit ? 'Edit Ticket Scheme' : 'Create Ticket Scheme';
$schemeOptions = TicketSchemeEnum::cases();
$languageOptions = TicketLanguageEnum::cases();
?>

<section class="mx-auto max-w-4xl p-8">
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Ticket Schemes</p>
            <h1 class="mb-2 text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                <?= htmlspecialchars($pageTitle) ?>
            </h1>
            <p class="text-gray-600">Reusable pricing scheme for schedule ticket types.</p>
        </div>
        <a href="/cms/ticket-schemes"
           class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50">
            Back to Schemes
        </a>
    </header>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
            <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($action) ?>">
        <div class="mb-6 rounded-lg border bg-white p-6">
            <h2 class="mb-4 border-b pb-2 text-xl font-bold">Basic Information</h2>

            <div class="mb-4">
                <label class="mb-2 block font-semibold text-gray-700" for="name">
                    Scheme Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       value="<?= htmlspecialchars($ticketScheme?->name ?? '') ?>"
                       class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g. Dance Club Entry">
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="scheme_enum">
                        Scheme Type <span class="text-red-500">*</span>
                    </label>
                    <select id="scheme_enum" name="scheme_enum" required
                            class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select scheme type —</option>
                        <?php foreach ($schemeOptions as $schemeOption): ?>
                            <option value="<?= htmlspecialchars($schemeOption->value) ?>"
                                <?= $ticketScheme?->scheme_enum === $schemeOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($schemeOption->value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="ticket_language">Ticket Language</label>
                    <select id="ticket_language" name="ticket_language"
                            class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">— No language restriction —</option>
                        <?php foreach ($languageOptions as $languageOption): ?>
                            <option value="<?= htmlspecialchars($languageOption->value) ?>"
                                <?= $ticketScheme?->ticket_language === $languageOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($languageOption->value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-lg border bg-white p-6">
            <h2 class="mb-4 border-b pb-2 text-xl font-bold">Pricing</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block font-semibold text-gray-700" for="price">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required
                           value="<?= htmlspecialchars((string)($ticketScheme?->price ?? '')) ?>"
                           class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g. 25.00">
                </div>
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    Reservation fee is intentionally not managed here. That remains Yummy-specific.
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white transition hover:bg-blue-700">
                <?= $isEdit ? 'Save Ticket Scheme' : 'Create Ticket Scheme' ?>
            </button>
            <a href="/cms/ticket-schemes"
               class="rounded-lg bg-gray-100 px-8 py-3 font-semibold text-gray-700 transition hover:bg-gray-200">
                Cancel
            </a>
        </div>
    </form>
</section>