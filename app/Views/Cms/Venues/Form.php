<?php

namespace App\Views\Cms\Venues;

use App\Models\Venue;

/** @var Venue|null $venue */
$venue = $venue ?? null;
$isEdit = $venue !== null;
$pageTitle = $isEdit ? "Edit Venue: {$venue->name}" : "Create New Venue";
$action = $action ?? '/cms/venues/store';
?>

<section class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update venue information' : 'Add a new venue to your festival' ?>
        </p>
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

    <!-- Form -->
    <form method="POST" action="<?= htmlspecialchars($action) ?>" enctype="multipart/form-data">

        <!-- Basic Info -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Basic Information</h2>

            <!-- Venue Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="name">
                    Venue Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($venue->name ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="description_html">
                    Description
                </label>
                <textarea id="description_html" name="description_html" rows="4"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Describe this venue..."><?= htmlspecialchars($venue->description_html ?? '') ?></textarea>
            </div>

            <!-- Capacity -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="capacity">
                    Capacity
                </label>
                <input type="number" id="capacity" name="capacity"
                    value="<?= htmlspecialchars($venue->capacity ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., 500" min="0">
                <p class="text-sm text-gray-500 mt-1">Leave blank for open air venues</p>
            </div>
        </div>

        <!-- Address -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Address</h2>

            <!-- Street Address -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="street_address">
                    Street Address <span class="text-red-500">*</span>
                </label>
                <input type="text" id="street_address" name="street_address"
                    value="<?= htmlspecialchars($venue->street_address ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Grote Markt 16" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- City -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="city">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($venue->city ?? 'Haarlem') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <!-- Postal Code -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="postal_code">
                        Postal Code
                    </label>
                    <input type="text" id="postal_code" name="postal_code"
                        value="<?= htmlspecialchars($venue->postal_code ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., 2000 AB">
                </div>
            </div>

            <!-- Country -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="country">
                    Country
                </label>
                <input type="text" id="country" name="country" value="<?= htmlspecialchars($venue->country ?? 'NL') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Contact Information</h2>

            <!-- Phone -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="phone">
                    Phone
                </label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($venue->phone ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="+31 23 123 4567">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="email">
                    Email
                </label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($venue->email ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="info@venue.com">
            </div>
        </div>

        <!-- Venue Image -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Venue Image</h2>

            <?php if ($venue && $venue->venue_image && $venue->venue_image->file_path): ?>
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Current Image:</p>
                    <?php
                    $imagePath = $venue->venue_image->file_path;
                    // Ensure leading slash
                    if (!str_starts_with($imagePath, '/')) {
                        $imagePath = '/' . $imagePath;
                    }
                    ?>
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($venue->name) ?>"
                        class="w-48 h-32 rounded-lg object-cover border"
                        onerror="this.onerror=null; this.src='/Assets/Home/ImagePlaceholder.png';">
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="venue_image">
                    <?= $isEdit ? 'Replace Image' : 'Upload Image' ?>
                </label>
                <input type="file" id="venue_image" name="venue_image" accept="image/jpeg,image/png,image/webp"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Max 5MB • JPG, PNG, or WebP</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                <?= $isEdit ? 'Update Venue' : 'Create Venue' ?>
            </button>
            <a href="/cms/venues"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</section>