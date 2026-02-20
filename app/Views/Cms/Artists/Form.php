<?php
namespace App\Views\Cms\Artists;

use App\Models\MusicEvent\Artist;

/** @var Artist|null $artist */
$artist = $artist ?? null;
$isEdit = $artist !== null;
$pageTitle = $isEdit ? "Edit Artist: {$artist->name}" : "Create New Artist";
$action = $action ?? '/cms/artists/store';
?>

<section class="p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update artist information' : 'Add a new artist to your festival' ?>
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

            <!-- Artist Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="name">
                    Artist Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?= htmlspecialchars($artist->name ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            <!-- Bio -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="bio">
                    Biography
                </label>
                <textarea id="bio" 
                          name="bio" 
                          rows="5"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tell us about this artist..."><?= htmlspecialchars($artist->bio ?? '') ?></textarea>
            </div>

            <!-- Featured Quote -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="featured_quote">
                    Featured Quote
                </label>
                <input type="text" 
                       id="featured_quote" 
                       name="featured_quote" 
                       value="<?= htmlspecialchars($artist->featured_quote ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="A memorable quote from the artist">
            </div>
        </div>

        <!-- Profile Image -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Profile Image</h2>

            <?php if ($artist && $artist->profile_image && $artist->profile_image->file_path): ?>
<div class="mb-4">
    <p class="text-sm font-semibold text-gray-700 mb-2">Current Image:</p>
    <?php
        $imagePath = $artist->profile_image->file_path;
            if (!str_starts_with($imagePath, '/')) {
            $imagePath = '/' . $imagePath;
        }
    ?>
    <img src="<?= htmlspecialchars($imagePath) ?>" 
         alt="<?= htmlspecialchars($artist->name) ?>"
         class="w-32 h-32 rounded-lg object-cover border"
         onerror="this.onerror=null; this.src='/Assets/Home/ImagePlaceholder.png';">
</div>
<?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="profile_image">
                    <?= $isEdit ? 'Replace Image' : 'Upload Image' ?>
                </label>
                <input type="file" 
                       id="profile_image" 
                       name="profile_image"
                       accept="image/jpeg,image/png,image/webp"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Max 5MB • JPG, PNG, or WebP</p>
            </div>
        </div>

        <!-- Social Links -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Social & Music Links</h2>

            <!-- Website -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="website">
                    Website
                </label>
                <input type="url" 
                       id="website" 
                       name="website" 
                       value="<?= htmlspecialchars($artist->website ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://artistwebsite.com">
            </div>

            <!-- Spotify -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="spotify_url">
                    Spotify URL
                </label>
                <input type="url" 
                       id="spotify_url" 
                       name="spotify_url" 
                       value="<?= htmlspecialchars($artist->spotify_url ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://open.spotify.com/artist/...">
            </div>

            <!-- YouTube -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="youtube_url">
                    YouTube URL
                </label>
                <input type="url" 
                       id="youtube_url" 
                       name="youtube_url" 
                       value="<?= htmlspecialchars($artist->youtube_url ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://youtube.com/channel/...">
            </div>

            <!-- SoundCloud -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="soundcloud_url">
                    SoundCloud URL
                </label>
                <input type="url" 
                       id="soundcloud_url" 
                       name="soundcloud_url" 
                       value="<?= htmlspecialchars($artist->soundcloud_url ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://soundcloud.com/artist">
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                <?= $isEdit ? 'Update Artist' : 'Create Artist' ?>
            </button>
            <a href="/cms/artists" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</section>