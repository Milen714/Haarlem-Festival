<?php
namespace App\Views\Cms\Schedules;

use App\Models\Schedule;

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
?>

<section class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
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
        <div class="bg-white border rounded-lg p-6 mb-6">
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
        <div class="bg-white border rounded-lg p-6 mb-6">
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

            <div class="grid grid-cols-2 gap-4">
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
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Capacity</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
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
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Link to (optional)</h2>
            <p class="text-sm text-gray-500 mb-4">Link this schedule slot to an artist, restaurant, or landmark. Only one can be active at a time.</p>

            <!-- Artist -->
            <div class="mb-4">
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
            <div class="mb-4">
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
            <div class="mb-4">
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
        </div>

        <!-- Submit -->
        <div class="flex gap-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                <?= $isEdit ? 'Save Changes' : 'Create Schedule' ?>
            </button>
            <a href="/cms/schedules"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</section>
