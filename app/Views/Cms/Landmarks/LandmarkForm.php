<?php
namespace App\Views\Cms\Landmarks;

use App\Models\History\Landmark;

/** @var Landmark|null $landmark */
$landmark = $landmark ?? null;
$isEdit = $landmark !== null;
$pageTitle = $isEdit ? "Edit Landmark: {$landmark->name}" : "Create New Landmark";
$action = $action ?? '/cms/landmarks/store';
$images = $images ?? []; 
?>

<section class="p-8 max-w-5xl mx-auto">
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update history tour stop information' : 'Add a new stop to the history tour' ?>
        </p>
    </header>

    <form method="POST" action="<?= htmlspecialchars($action) ?>" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
        <input type="hidden" name="landmark_id" value="<?= $landmark->landmark_id ?>">
        <?php endif; ?>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Landmark Information:</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($landmark->name ?? '') ?>" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Display Order</label>
                    <input type="number" name="display_order"
                        value="<?= htmlspecialchars($landmark->display_order ?? '0') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Short Description (For Homepage Cards)</label>
                <textarea name="short_description" rows="2" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($landmark->short_description ?? '') ?></textarea>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Map Coordinates</h2>
            <p class="text-sm text-gray-600 mb-4">Precise latitude and longitude for map display and location tracking.
            </p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Latitude</label>
                    <input type="number" name="latitude" step="any"
                        value="<?= htmlspecialchars($landmark->latitude ?? '') ?>" placeholder="e.g., 52.3876"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Decimal format</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Longitude</label>
                    <input type="number" name="longitude" step="any"
                        value="<?= htmlspecialchars($landmark->longitude ?? '') ?>" placeholder="e.g., 4.6364"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Decimal format</p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Detail Page Content</h2>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Introduction Title</label>
                <input type="text" name="intro_title"
                    value="<?= htmlspecialchars($landmark->intro_title ?? 'Introduction') ?>"
                    class="w-full px-4 py-2 mb-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <textarea name="intro_content"
                    class="tinymce-editor"><?= htmlspecialchars($landmark->intro_content ?? '') ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">History Title</label>
                <input type="text" name="detail_history_title"
                    value="<?= htmlspecialchars($landmark->detail_history_title ?? 'History') ?>"
                    class="w-full px-4 py-2 mb-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <textarea name="detail_history_content"
                    class="tinymce-editor"><?= htmlspecialchars($landmark->detail_history_content ?? '') ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Why to Visit Title</label>
                <input type="text" name="why_visit_title"
                    value="<?= htmlspecialchars($landmark->why_visit_title ?? 'Practical Information') ?>"
                    class="w-full px-4 py-2 mb-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <textarea name="why_visit_content"
                    class="tinymce-editor"><?= htmlspecialchars($landmark->why_visit_content ?? '') ?></textarea>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Page Images</h2>
            <p class="text-sm text-gray-600 mb-6">Assign a specific image for each section. Existing images will be
                replaced upon new upload.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($images as $slot): ?>
                <?php $media = $slot['media']; ?>

                <div class="border rounded-lg p-4 bg-gray-50 flex flex-col">
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">
                        <?= htmlspecialchars($slot['label']) ?> Image
                    </label>

                    <div
                        class="mb-3 aspect-video bg-white border rounded overflow-hidden flex items-center justify-center">
                        <?php if ($media && !empty($media->file_path)): ?>
                        <img src="<?= htmlspecialchars('/' . ltrim($media->file_path, '/')) ?>"
                            class="w-full h-full object-cover">
                        <input type="hidden" name="<?= htmlspecialchars($slot['name']) ?>_id"
                            value="<?= $media->media_id ?>">
                        <?php else: ?>
                        <span class="text-gray-300 text-[10px]">No image uploaded</span>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="<?= htmlspecialchars($slot['name']) ?>" accept="image/*"
                        class="text-[10px] w-full">

                    <?php if ($media): ?>
                    <p class="text-[9px] text-blue-600 mt-1 italic">Uploading a new file will delete the current one.
                    </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                <?= $isEdit ? 'Update Landmark' : 'Create Landmark' ?>
            </button>
            <a href="/cms/landmarks"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>

    </form>
</section>

<script>
if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: '.tinymce-editor',
        height: 300,
        menubar: false,
        plugins: 'lists link image code',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | code'
    });
}
</script>