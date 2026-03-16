<?php

namespace App\Views\Cms\TicketSchemes;

$ticketSchemes = $ticketSchemes ?? [];
$usageCounts = $usageCounts ?? [];
?>

<section class="mx-auto max-w-7xl p-8">
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Ticket Schemes</p>
            <h1 class="mb-2 text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                Manage Ticket Schemes
            </h1>
            <p class="text-gray-600">Create reusable pricing schemes that ticket types can attach to.</p>
        </div>
        <div class="flex gap-3">
            <a href="/cms/schedules"
                class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50">
                Back to Schedules
            </a>
            <a href="/cms/ticket-schemes/create"
                class="rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white transition hover:bg-green-700">
                + Add Ticket Scheme
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

    <?php if (isset($error)): ?>
        <div class="mb-6 rounded border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
            <p class="font-medium">✗ <?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <?php if (empty($ticketSchemes)): ?>
        <div class="rounded-lg border bg-white p-10 text-center shadow-sm">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">No Ticket Schemes Yet</h2>
            <p class="mb-6 text-gray-600">Start by creating a reusable scheme for your ticket types.</p>
            <a href="/cms/ticket-schemes/create"
                class="inline-flex rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white transition hover:bg-emerald-700">
                Create First Scheme
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <table class="w-full">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Language</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($ticketSchemes as $ticketScheme): ?>
                        <?php $usageCount = (int)($usageCounts[$ticketScheme->ticket_scheme_id] ?? 0); ?>
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($ticketScheme->name ?? 'Unnamed scheme') ?>
                                <?php if ($usageCount > 0): ?>
                                    <div class="mt-1 text-xs font-normal text-amber-700">
                                        Used by <?= $usageCount ?> ticket type(s)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= htmlspecialchars($ticketScheme->scheme_enum?->value ?? 'No enum') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                €<?= number_format((float)($ticketScheme->price ?? 0), 2) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= htmlspecialchars($ticketScheme->ticket_language?->value ?? 'Not set') ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="/cms/ticket-schemes/edit/<?= (int)$ticketScheme->ticket_scheme_id ?>"
                                        class="rounded bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                        Edit
                                    </a>
                                    <?php if ($usageCount > 0): ?>
                                        <span class="rounded bg-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-500"
                                            title="This scheme is in use and cannot be deleted.">
                                            In Use
                                        </span>
                                    <?php else: ?>
                                        <form method="POST"
                                            action="/cms/ticket-schemes/delete/<?= (int)$ticketScheme->ticket_scheme_id ?>"
                                            onsubmit="return confirm('Delete this ticket scheme? This cannot be undone.')">
                                            <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-sm text-gray-500">Schemes linked to ticket types are protected from deletion.</p>
    <?php endif; ?>
</section>