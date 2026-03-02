<?php
namespace App\Views\Cms\Landmarks;

use App\Models\History\Landmark;

/** @var Landmark[] $landmarks */
$landmarks = $landmarks ?? [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Manage Landmarks
            </h1>
            <p class="text-gray-600">History Main Landmarks</p>
        </div>
        <a href="/cms/landmarks/create" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            Add New Landmark
        </a>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <p class="font-medium">Success <?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
        <p class="font-medium">Error <?= htmlspecialchars($_SESSION['error']) ?></p>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($landmarks)): ?>
        <div class="bg-white border rounded-lg p-12 text-center">
            <div class="text-6xl mb-4"></div>
            <h3 class="text-2xl font-bold mb-2">No Landmarks To Display</h3>
            <a href="/cms/landmarks/create" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
               Add Landmark
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php foreach ($landmarks as $landmark): ?>
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition flex flex-col h-full">
                
                <div class="bg-gray-50 border-b px-6 py-4 flex justify-between items-center">
                    <span class="inline-block bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold">
                        Order: <?= $landmark->display_order ?>
                    </span>
                    <span class="text-sm text-blue-600 font-mono truncate ml-2" title="/<?= htmlspecialchars($landmark->landmark_slug) ?>">
                        /<?= htmlspecialchars($landmark->landmark_slug) ?>
                    </span>
                </div>

                <div class="px-6 py-5 flex-grow">
                    <p class="font-bold text-xl text-gray-900 mb-2">
                        <?= htmlspecialchars($landmark->name) ?>
                    </p>
                    <p class="text-sm text-gray-500 line-clamp-3">
                        <?= htmlspecialchars($landmark->short_description) ?>
                    </p>
                </div>

                <div class="bg-gray-50 border-t px-6 py-4 flex gap-2 justify-end mt-auto">
                    <a href="/history/detail/<?= $landmark->landmark_slug ?>" target="_blank"
                       class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-4 py-2 rounded transition text-sm">
                        View
                    </a>
                    <a href="/cms/landmarks/edit/<?= $landmark->landmark_id ?>" 
                       class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded transition text-sm">
                        Edit
                    </a>
                    <form method="POST" action="/cms/landmarks/delete/<?= $landmark->landmark_id ?>" 
                          onsubmit="return confirm('Once deleted the information is not recoverable. Are you sure you want to delete? <?= htmlspecialchars($landmark->name) ?>?');" class="inline">
                        <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded transition text-sm">
                            Delete
                        </button>
                    </form>
                </div>
                
            </div>
            <?php endforeach; ?>
            
        </div>
    <?php endif; ?>
</section>