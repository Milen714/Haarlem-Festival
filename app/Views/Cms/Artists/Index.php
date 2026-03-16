<?php

namespace App\Views\Cms\Artists;

use App\Models\MusicEvent\Artist;

/** @var Artist[] $artists */
$artists = $artists ?? [];
?>

<section class="mx-auto max-w-7xl p-4 md:p-8">
    <!-- Header -->
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Manage Artists
            </h1>
            <p class="text-gray-600">All performers and musicians</p>
        </div>
        <a href="/cms/artists/create"
            class="w-full rounded-lg bg-green-600 px-6 py-3 text-center font-semibold text-white transition hover:bg-green-700 md:w-auto">
            + Add New Artist
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

    <!-- Artists Table -->
    <?php if (empty($artists)): ?>
        <div class="bg-white border rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">🎤</div>
            <h3 class="text-2xl font-bold mb-2">No Artists Yet</h3>
            <p class="text-gray-600 mb-6">Start by adding your first artist</p>
            <a href="/cms/artists/create"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First Artist
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Artist</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Bio</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Links</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($artists as $artist): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Artist Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Fixed Size Image Box -->
                                    <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded border border-gray-300 bg-gray-100">
                                        <?php if ($artist->hasProfileImage()): ?>
                                            <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>"
                                                alt="<?= htmlspecialchars($artist->getProfileImageAlt()) ?>"
                                                class="w-full h-full object-cover"
                                                style="width: 64px; height: 64px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-purple-200 flex items-center justify-center">
                                                <span class="text-purple-700 font-bold text-xl">
                                                    <?= $artist->getInitial() ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Text Content -->
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($artist->name) ?></p>
                                        <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($artist->slug) ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Bio -->
                            <td class="px-6 py-4">
                                <?php if ($artist->bio): ?>
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        <?= htmlspecialchars(substr(strip_tags($artist->bio), 0, 100)) ?>...
                                    </p>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm italic">No bio</span>
                                <?php endif; ?>
                            </td>

                            <!-- Links -->
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <?php if ($artist->website): ?>
                                        <a href="<?= htmlspecialchars($artist->website) ?>"
                                            target="_blank"
                                            class="text-gray-400 hover:text-gray-600 transition"
                                            title="Website">
                                            🌐
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($artist->spotify_url): ?>
                                        <a href="<?= htmlspecialchars($artist->spotify_url) ?>"
                                            target="_blank"
                                            class="text-green-500 hover:text-green-600 transition"
                                            title="Spotify">
                                            🎵
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($artist->youtube_url): ?>
                                        <a href="<?= htmlspecialchars($artist->youtube_url) ?>"
                                            target="_blank"
                                            class="text-red-500 hover:text-red-600 transition"
                                            title="YouTube">
                                            ▶️
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="/cms/artists/edit/<?= $artist->artist_id ?>"
                                        class="rounded bg-blue-100 px-4 py-2 text-blue-700 transition hover:bg-blue-200">
                                        Edit
                                    </a>
                                    <form method="POST"
                                        action="/cms/artists/delete/<?= $artist->artist_id ?>"
                                        onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($artist->name) ?>?');"
                                        class="inline">
                                        <button type="submit"
                                            class="rounded bg-red-100 px-4 py-2 text-red-700 transition hover:bg-red-200">
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
            Total Artists: <strong><?= count($artists) ?></strong>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="/cms" class="text-gray-600 hover:text-gray-900 transition">
            ← Back to Dashboard
        </a>
    </div>
</section>