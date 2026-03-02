<?php
namespace App\Views\Cms;
$pageLinks = isset($pageSlugs) ? $pageSlugs : [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2">CMS Dashboard</h1>
        <p class="text-gray-600">Manage your festival content, artists, and venues</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Page Management -->
        <article class="bg-white border rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-4">📄</span>
                <h2 class="text-2xl font-bold">Pages</h2>
            </div>
            <p class="text-gray-600 mb-4">Edit website pages and content</p>
            <div class="space-y-2">
                <?php foreach ($pageLinks as $slug): ?>
                <a href="/cms/page/edit/<?= htmlspecialchars($slug['slug']) ?>"
                    class="block text-blue-600 hover:underline font-semibold"> <span class="text-yellow-400">→
                        Edit</span>
                    <?= htmlspecialchars($slug['title']) ?></a>
                <?php endforeach; ?>
            </div>
        </article>

        <!-- Artist Management -->
        <article class="bg-white border rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-4">🎤</span>
                <h2 class="text-2xl font-bold">Artists</h2>
            </div>
            <p class="text-gray-600 mb-4">Manage performers and musicians</p>
            <div class="space-y-2">
                <a href="/cms/artists" class="block text-blue-600 hover:underline font-semibold">→ View All Artists</a>
                <a href="/cms/artists/create" class="block text-green-600 hover:underline">+ Add New Artist</a>
            </div>
        </article>

        <!-- Venue Management -->
        <article class="bg-white border rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-4">🏛️</span>
                <h2 class="text-2xl font-bold">Venues</h2>
            </div>
            <p class="text-gray-600 mb-4">Manage event locations</p>
            <div class="space-y-2">
                <a href="/cms/venues" class="block text-blue-600 hover:underline font-semibold">→ View All Venues</a>
                <a href="/cms/venues/create" class="block text-green-600 hover:underline">+ Add New Venue</a>
            </div>
        </article>

        <!--Landmark Management -->
        <article class="bg-white border rounded-lg p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-4xl mr-4">🏰</span>
                <h2 class="text-2xl font-bold">Landmarks</h2>
            </div>
            <p class="text-gray-600 mb-4">Manage landmarks</p>
            <div class="space-y-2">
                <a href="/cms/landmarks" class="block text-blue-600 hover:underline font-semibold">→ View All Landmarks</a>
                <a href="/cms/landmarks/create" class="block text-green-600 hover:underline">+ Add New Landmark</a>
            </div>
        </article>

    </div>
</section>