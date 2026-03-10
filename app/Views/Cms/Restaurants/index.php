<?php 
namespace App\Views\Cms\Restaurants;

use App\Models\Restaurant;
/**
 * @var Restaurant[] $restaurants
 */
$restaurants = $restaurants ?? [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2">
                Manage Restaurants
            </h1>
            <p class="text-gray-600">All participating restaurants</p>
        </div>

        <a href="/cms/restaurants/create"
        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            + Add New Restaurant
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
     <?php if (empty($restaurants)): ?>

        <div class="bg-white border rounded-lg p-12 text-center">

            <div class="text-6xl mb-4">🍽️</div>

            <h3 class="text-2xl font-bold mb-2">No Restaurants Yet</h3>

            <p class="text-gray-600 mb-6">
                Start by adding your first restaurant
            </p>

            <a href="/cms/restaurants/create"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First Restaurant
            </a>

        </div>

    <?php else: ?>
        <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Restaurant</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Discription</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Venue</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Rating</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <?php foreach ($restaurants as $restaurant): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Restaurant Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded border bg-gray-100">
                                        <?php if ($restaurant->main_image): ?>
                                            <img src="<?= htmlspecialchars($restaurant->main_image->file_path) ?>"
                                            alt="<?= htmlspecialchars($restaurant->main_image->alt_text ?? $restaurant->name) ?>"
                                            class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img src="/app/public/Assets/Yummy/Restaurant/placeholder.png"
                                            alt="place holder"
                                            class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <p class="font-semibold text-gray-900 truncate">
                                        <?= $restaurant->name ?>
                                    </p>
                                </div>
                            </td>
                            <!-- Bio -->
                            <td>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-500 truncate">
                                        <?= substr(strip_tags($restaurant->welcome_text), 0, 20) ?? 'No description' ?>
                                    </p>
                                </div> 
                            </td>
                            <!-- Venue -->
                            <td class="px-6 py-4">
                                <?php if ($restaurant->venue): ?>
                                    <p class="font-medium text-gray-800">
                                        <?= htmlspecialchars($restaurant->venue->name) ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($restaurant->venue->city) ?>
                                    </p>
                                <?php else: ?>
                                    <span class="text-gray-400 italic text-sm">
                                    No venue
                                    </span>
                                <?php endif; ?>
                            </td>
                            <!-- Rating -->
                            <td class="px-6 py-4">
                                <?php if ($restaurant->stars): ?>
                                    <span class="text-yellow-500">
                                        <?= str_repeat('★', floor($restaurant->stars)) ?>
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        (<?= $restaurant->review_count ?? 0 ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">
                                        No rating
                                    </span>
                                <?php endif; ?>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="/cms/restaurants/edit/<?= $restaurant->restaurant_id ?>"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST"
                                        action="/cms/restaurants/delete/<?= $restaurant->restaurant_id ?>"
                                        onsubmit="return confirm('Delete <?= htmlspecialchars($restaurant->name) ?>?');"
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
            Total Restaurants:
            <strong><?= count($restaurants) ?></strong>
        </div>

    <?php endif; ?>
    
    <div class="mt-8">
        <a href="/cms" class="text-gray-600 hover:text-gray-900 transition">
            ← Back to Dashboard
        </a>
    </div>

</section>