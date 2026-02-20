<?php
namespace App\Views\Cms\Venues;

use App\Models\Venue;

/** @var Venue[] $venues */
$venues = $venues ?? [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Manage Venues
            </h1>
            <p class="text-gray-600">All event locations</p>
        </div>
        <a href="/cms/venues/create" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            + Add New Venue
        </a>
    </header>

    <!-- Success/Error Messages -->
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

    <!-- Venues Table -->
    <?php if (empty($venues)): ?>
        <div class="bg-white border rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">🏛️</div>
            <h3 class="text-2xl font-bold mb-2">No Venues Yet</h3>
            <p class="text-gray-600 mb-6">Start by adding your first venue</p>
            <a href="/cms/venues/create" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First Venue
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Venue</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Address</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Capacity</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Contact</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($venues as $venue): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Venue Info -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <!-- Fixed Size Image Box -->
                                <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded border border-gray-300 bg-gray-100">
                                    <?php if ($venue->hasImage()): ?>
                                       <img src="<?= htmlspecialchars($venue->getImagePath()) ?>" 
                                       alt="<?= htmlspecialchars($venue->getImageAlt()) ?>"
                                       class="w-full h-full object-cover"
                                       style="width: 64px; height: 64px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-blue-200 flex items-center justify-center">
                                            <span class="text-blue-700 font-bold text-xl">
                                                <?= strtoupper(substr($venue->name, 0, 1)) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Text Content -->
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($venue->name) ?></p>
                                    <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($venue->city) ?></p>
                                </div>
                            </div>
                        </td>

                        <!-- Address -->
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">
                                <?= htmlspecialchars($venue->street_address) ?><br>
                                <?= htmlspecialchars($venue->postal_code ?? '') ?> <?= htmlspecialchars($venue->city) ?>
                            </p>
                        </td>

                        <!-- Capacity -->
                        <td class="px-6 py-4">
                            <?php if ($venue->capacity): ?>
                                <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                                    <?= number_format($venue->capacity) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm italic">Not set</span>
                            <?php endif; ?>
                        </td>

                        <!-- Contact -->
                        <td class="px-6 py-4">
                            <?php if ($venue->email || $venue->phone): ?>
                                <div class="text-sm text-gray-600">
                                    <?php if ($venue->email): ?>
                                        <div>📧 <?= htmlspecialchars($venue->email) ?></div>
                                    <?php endif; ?>
                                    <?php if ($venue->phone): ?>
                                        <div>📞 <?= htmlspecialchars($venue->phone) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm italic">No contact</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-2 justify-end">
                                <a href="/cms/venues/edit/<?= $venue->venue_id ?>" 
                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded transition">
                                    Edit
                                </a>
                                <form method="POST" 
                                      action="/cms/venues/delete/<?= $venue->venue_id ?>" 
                                      onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($venue->name) ?>?');"
                                      class="inline">
                                    <button type="submit" 
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded transition">
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
        <div class="mt-4 text-center text-gray-600">
            Total Venues: <strong><?= count($venues) ?></strong>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="/cms" class="text-gray-600 hover:text-gray-900 transition">
            ← Back to Dashboard
        </a>
    </div>
</section>