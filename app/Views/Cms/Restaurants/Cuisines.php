<?php

namespace App\Views\Cms\Restaurants;

use App\Models\Cuisine;

/**
 * @var Cuisine[] $cuisines
 */
$cuisines = $cuisines ?? [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2">
                Manage Cuisines
            </h1>
            <p class="text-gray-600">All Current Cuisines Options</p>
        </div>

        <a href="/cms/restaurants/cuisines/create"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            + Add New Cuisine
        </a>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
        </div>
    <?php
        unset($_SESSION['success']);
    endif;
    ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['error']) ?></p>
        </div>
    <?php
        unset($_SESSION['error']);
    endif;
    ?>

    <!-- Restaurant table -->
    <?php if (empty($cuisines)): ?>

        <div class="bg-white border rounded-lg p-12 text-center">

            <div class="text-6xl mb-4">🍽️</div>

            <h3 class="text-2xl font-bold mb-2">No Cuisines Yet</h3>

            <p class="text-gray-600 mb-6">
                Start by adding your first Cuisine
            </p>

            <a href="/cms/restaurants/cuisines/create"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First Cuisine
            </a>

        </div>

    <?php else: ?>
        <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Cuisne</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Description</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Icon</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <?php foreach ($cuisines as $cuisine): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Restaurant Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">

                                    <p class="font-semibold text-gray-900 truncate">
                                        <?= htmlspecialchars($cuisine->name) ?>
                                    </p>
                                </div>
                            </td>
                            <!-- Bio -->
                            <td>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-500 truncate">
                                        <?= substr(strip_tags($cuisine->description), 0, 20) ?? 'No description' ?>
                                    </p>
                                </div>
                            </td>
                            <!-- Venue -->
                            <td class="px-6 py-4">

                                <p class="font-medium text-gray-800">
                                    <?= htmlspecialchars($cuisine->icon) ?>
                                </p>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="/cms/restaurants/cuisines/edit/<?= $cuisine->cuisine_Id ?>"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST"
                                        action="/cms/restaurants/cuisines/delete/<?= $cuisine->cuisine_Id ?>"
                                        onsubmit="return confirm('Delete <?= htmlspecialchars($cuisine->name) ?>?');"
                                        class="inline">

                                        <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded transition">
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
        <!-- Summary -->

        <div class="mt-4 text-center text-gray-600">\
            Total Cuisines:
            <strong><?= count($cuisines) ?></strong>
        </div>

    <?php endif; ?>

    <div class="mt-8">
        <a href="/cms" class="text-gray-600 hover:text-gray-900 transition">
            ← Back to Dashboard
        </a>
    </div>

</section>