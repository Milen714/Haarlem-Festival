<?php

namespace App\Views\Cms\Artists;

use App\Models\MusicEvent\Artist;

/** @var Artist|null $artist */
$artist = $artist ?? null;
$isEdit = $artist !== null;
$pageTitle = $isEdit ? "Edit Artist: {$artist->name}" : "Create New Artist";
$action = $action ?? '/cms/artists/store';
?>

<section class="mx-auto max-w-4xl p-4 md:p-8">
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
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($artist->name ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" id="special_event" name="special_event" value="1"
                            <?= ($artist->special_event ?? false) ? 'checked' : '' ?>
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <span class="text-gray-700 font-semibold">Special Event / Headliner</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-8">Check this if the artist should be featured in the special events or headliners section.</p>
            </div>

            <!-- Bio -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="bio">
                    Biography
                </label>
                <textarea id="bio" name="bio" rows="5"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Tell us about this artist..."><?= htmlspecialchars($artist->bio ?? '') ?></textarea>
            </div>

            <!-- Featured Quote -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="featured_quote">
                    Featured Quote
                </label>
                <input type="text" id="featured_quote" name="featured_quote"
                    value="<?= htmlspecialchars($artist->featured_quote ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="A memorable quote from the artist">
            </div>

            <!-- Press Quote -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="press_quote">
                    Press Quote
                </label>
                <input type="text" id="press_quote" name="press_quote"
                    value="<?= htmlspecialchars($artist->press_quote ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="What the press says about this artist">
            </div>

            <!-- Collaborations -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="collaborations">
                    Collaborations
                </label>
                <textarea id="collaborations" name="collaborations" rows="3"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Featured artists & collaborators..."><?= htmlspecialchars($artist->collaborations ?? '') ?></textarea>
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
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($artist->name) ?>"
                        class="w-32 h-32 rounded-lg object-cover border"
                        onerror="this.onerror=null; this.src='/Assets/Home/ImagePlaceholder.png';">
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="profile_image">
                    <?= $isEdit ? 'Replace Image' : 'Upload Image' ?>
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/webp"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Max 5MB • JPG, PNG, or WebP</p>
            </div>
        </div>

        <!-- Gallery Images -->
        <?php if ($isEdit): ?>
            <section aria-labelledby="gallery-heading" class="bg-white border rounded-lg p-6 mb-6">
                <h2 id="gallery-heading" class="text-xl font-bold mb-1 border-b pb-2">Gallery Images</h2>
                <p class="text-sm text-gray-500 mb-4">These photos appear in the gallery section of the artist's page. You
                    can replace each existing image directly, or upload new images at the bottom.</p>

                <?php if ($artist->gallery && !empty($artist->gallery->media_items)): ?>
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        Current Gallery (<?= count($artist->gallery->media_items) ?>
                        image<?= count($artist->gallery->media_items) !== 1 ? 's' : '' ?>):
                    </p>
                    <ul class="mb-5 grid list-none grid-cols-1 gap-4 p-0 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <?php foreach ($artist->gallery->media_items as $imgIndex => $gm): ?>
                            <?php
                            $imgPath  = $gm->media?->file_path ?? '';
                            if ($imgPath && !str_starts_with($imgPath, '/')) {
                                $imgPath = '/' . $imgPath;
                            }
                            $imgAlt   = $gm->media?->alt_text ?? ($artist->name . ' gallery image');
                            $mediaId  = $gm->media?->media_id ?? null;
                            $position = $imgIndex + 1;
                            $total    = count($artist->gallery->media_items);
                            ?>
                            <?php if ($mediaId): ?>
                                <li class="rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-50">
                                    <figure class="flex flex-col h-full m-0">
                                        <span class="sr-only">Photo <?= $position ?> of <?= $total ?></span>
                                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($imgAlt) ?>"
                                            class="w-full h-40 object-cover block"
                                            onerror="this.onerror=null; this.src='/Assets/Home/ImagePlaceholder.png';">
                                        <figcaption
                                            class="flex items-center justify-between px-2 py-2 bg-white border-t border-gray-100 text-xs text-gray-500">
                                            <span>#<?= $position ?> of <?= $total ?></span>
                                            <button type="submit"
                                                formaction="/cms/artists/gallery-remove/<?= (int)$artist->artist_id ?>/<?= (int)$mediaId ?>"
                                                formmethod="POST" formnovalidate
                                                onclick="return confirm('Remove photo #<?= $position ?> from the gallery?');"
                                                class="text-red-600 hover:text-red-800 font-semibold hover:underline">
                                                Remove
                                            </button>
                                        </figcaption>

                                        <div class="px-2 py-2 border-t border-gray-100 bg-gray-50">
                                            <label for="gallery_replace_<?= (int)$mediaId ?>"
                                                class="block text-[11px] font-semibold text-gray-700 mb-1">
                                                Replace this image
                                            </label>
                                            <input type="file" id="gallery_replace_<?= (int)$mediaId ?>"
                                                name="gallery_replace_<?= (int)$mediaId ?>" accept="image/jpeg,image/png,image/webp"
                                                class="text-[11px] w-full">
                                            <p class="text-[10px] text-blue-600 mt-1 italic">Uploading a new file replaces this image in
                                                place.</p>
                                        </div>
                                    </figure>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-400 italic mb-4">No gallery images yet. Upload some below.</p>
                <?php endif; ?>

                <fieldset class="border-0 p-0 m-0">
                    <label class="block text-gray-700 font-semibold mb-2" for="gallery_images">
                        Add New Gallery Images
                    </label>
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/jpeg,image/png,image/webp"
                        multiple
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">You can select multiple files at once • Max 5MB each • JPG, PNG,
                        or WebP</p>
                </fieldset>
            </section>
        <?php endif; ?>

        <!-- Social Links -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Social & Music Links</h2>

            <!-- Website -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="website">
                    Website
                </label>
                <input type="url" id="website" name="website" value="<?= htmlspecialchars($artist->website ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://artistwebsite.com">
            </div>

            <!-- Spotify -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="spotify_url">
                    Spotify URL
                </label>
                <input type="url" id="spotify_url" name="spotify_url"
                    value="<?= htmlspecialchars($artist->spotify_url ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://open.spotify.com/artist/...">
            </div>

            <!-- YouTube -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="youtube_url">
                    YouTube URL
                </label>
                <input type="url" id="youtube_url" name="youtube_url"
                    value="<?= htmlspecialchars($artist->youtube_url ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://youtube.com/channel/...">
            </div>

            <!-- SoundCloud -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="soundcloud_url">
                    SoundCloud URL
                </label>
                <input type="url" id="soundcloud_url" name="soundcloud_url"
                    value="<?= htmlspecialchars($artist->soundcloud_url ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://soundcloud.com/artist">
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
            <button type="submit"
                class="w-full rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                <?= $isEdit ? 'Update Artist' : 'Create Artist' ?>
            </button>
            <a href="/cms/artists"
                class="w-full rounded-lg bg-gray-200 px-8 py-3 text-center font-semibold text-gray-700 transition hover:bg-gray-300 sm:w-auto">
                Cancel
            </a>
        </div>
    </form>
</section>