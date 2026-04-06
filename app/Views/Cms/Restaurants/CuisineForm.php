<?php

use App\Models\Cuisine;

/** @var Cuisine|null $cuisine */
$cuisine = $cuisine ?? null;

$isEdit = $cuisine !== null;
$pageTitle = $isEdit 
    ? "Edit Cuisine: {$cuisine->name}" 
    : "Create New Cuisine";

$action = $action ?? '/cms/restaurants/cuisines/store';
?>

<section class="mx-auto max-w-3xl p-4 md:p-8">

    <!-- Header -->
    <header class="mb-8">
        <h1 class="text-3xl font-bold mb-2">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update cuisine details' : 'Add a new cuisine type' ?>
        </p>
    </header>

    <!-- Success/Error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 p-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 p-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="<?= htmlspecialchars($action) ?>">

        <div class="bg-white border rounded-lg p-6 mb-6">

            <!-- Name -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Cuisine Name *
                </label>
                <input 
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($cuisine->name ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border rounded-lg"
                >
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Description
                </label>
                <textarea 
                    name="description"
                    rows="4"
                    class="w-full px-4 py-2 border rounded-lg"
                ><?= htmlspecialchars($cuisine->description ?? '') ?></textarea>
            </div>

            <!-- Icon -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Icon (emoji or URL)
                </label>
                <input 
                    type="text"
                    name="icon_url"
                    value="<?= htmlspecialchars($cuisine->icon ?? '') ?>"
                    placeholder="🍝 or https://..."
                    class="w-full px-4 py-2 border rounded-lg"
                >
            </div>

        </div>

        <!-- Submit -->
        <div class="flex gap-4">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                <?= $isEdit ? 'Update Cuisine' : 'Create Cuisine' ?>
            </button>

            <a href="/cms/restaurants/cuisines"
               class="bg-gray-200 px-6 py-2 rounded-lg">
                Cancel
            </a>
        </div>

    </form>
</section>