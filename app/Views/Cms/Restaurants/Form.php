<?php

namespace App\Views\Cms\Restaurants;

use App\Models\Restaurant;
use App\Models\Venue;
use App\Models\Cuisine;

/** @var Restaurant|null $restaurant */
/** @var Venue[] $venues */

$restaurant = $restaurant ?? null;
$isEdit = $restaurant !== null;
$pageTitle = $isEdit ? "Edit Restaurant: {$restaurant->name}" : "Create New Restaurant";
$action = $action ?? '/cms/restaurants/store';
?>

<section class="p-8 max-w-4xl mx-auto">

    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
    </header>

    <form method="POST" action="<?= htmlspecialchars($action) ?>" enctype="multipart/form-data">

        <!-- Basic Info -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Basic Information</h2>

            <!-- Name -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Restaurant Name *</label>
                <input type="text"
                    name="name"
                    value="<?= htmlspecialchars($restaurant->name ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg"
                    required>
            </div>

            <!-- Short Description -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Short Description</label>
                <textarea name="short_description"
                    rows="3"
                    class="tinymce w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($restaurant->short_description ?? '') ?></textarea>
            </div>

            <!-- Welcome Text -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Welcome Text</label>
                <textarea name="welcome_text"
                    rows="4"
                    class="tinymce w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($restaurant->welcome_text ?? '') ?></textarea>
            </div>

        </div>


        <!-- Venue -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Venue</h2>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Venue</label>

                <select name="venue_id"
                    class="w-full px-4 py-2 border rounded-lg">

                    <option value="">Select venue</option>

                    <?php foreach ($venues as $venue): ?>

                        <option value="<?= $venue->venue_id ?>"
                            <?= ($restaurant && $restaurant->venue_id == $venue->venue_id) ? 'selected' : '' ?>>

                            <?= htmlspecialchars($venue->name) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>
        </div>


        <!-- Pricing -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Pricing & Rating</h2>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block font-semibold mb-2">Price Category</label>
                    <select name="price_category" class="w-full px-4 py-2 border rounded-lg">

                        <option value="">Select price level</option>

                        <option value="1" <?= ($restaurant->price_category ?? '') == 1 ? 'selected' : '' ?>>€</option>
                        <option value="2" <?= ($restaurant->price_category ?? '') == 2 ? 'selected' : '' ?>>€€</option>
                        <option value="3" <?= ($restaurant->price_category ?? '') == 3 ? 'selected' : '' ?>>€€€</option>

                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Stars</label>
                    <input type="number"
                        name="stars"
                        step="0.1"
                        min="0"
                        max="5"
                        value="<?= htmlspecialchars($restaurant->stars ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>

            </div>

        </div>
        <!-- Cusine Form -->
        <section class="be-white border rounded-lg p-6 mb-6">
            <h2 class="text-lx font-bold mb-4">Cuisine Types (Max 3)</h2>
            <?php foreach ($cuisines as $cuisine): ?>
                <label for="" class="flex items-center gap-2 mb-2">
                    <input
                        type="checkbox"
                        name="cuisines[]"
                        value="<?= $cuisine->cuisine_Id ?>"
                        <?= in_array($cuisine->cuisine_Id, array_map(fn($c) => $c->cuisine_Id, $restaurant->cuisines ?? [])) ? 'checked' : '' ?>
                        class="cuisine-checkbox">
                    <?= htmlspecialchars($cuisine->name) ?>
                </label>
            <?php endforeach ?>
        </section>

        <?php for ($i = 0; $i < 3; $i++):
            $session = $restaurant->sessions[$i] ?? null;
        ?>
            <div class="grid grid-cols-3 gap-4 mb-3">

                <select name="sessions[<?= $i ?>][type]" class="border px-3 py-2 rounded">
                    <option value="">Select type</option>
                    <?php foreach ($sessionTypes as $type): ?>
                        <option value="<?= $type['session_type_id'] ?>"
                            <?= (isset($session) && $session->session_id == $type['session_type_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="time"
                    name="sessions[<?= $i ?>][start]"
                    value="<?= $session?->start_time?->format('H:i') ?>"
                    class="border px-3 py-2 rounded">

                <input type="time"
                    name="sessions[<?= $i ?>][end]"
                    value="<?= $session?->end_time?->format('H:i') ?>"
                    class="border px-3 py-2 rounded">

            </div>
        <?php endfor ?>

        <!-- Images -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Images</h2>
            <!-- Main Image -->
            <div class="mb-6">
                <label class="block font-semibold mb-2">Main Image</label>
                <?php if ($restaurant && $restaurant->main_image): ?>
                    <img src="<?= htmlspecialchars($restaurant->main_image->file_path) ?>"
                        class="w-32 h-32 object-cover mb-2">
                <?php endif; ?>
                <input type="file"
                    name="main_image"
                    accept="image/jpeg,image/png,image/webp"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <!-- Chef Image -->
            <div class="mb-6">
                <label class="block font-semibold mb-2">Chef Image</label>

                <?php if ($restaurant && $restaurant->chef_img): ?>
                    <img src="<?= htmlspecialchars($restaurant->chef_img->file_path) ?>"
                        class="w-32 h-32 object-cover mb-2">
                <?php endif; ?>

                <input type="file"
                    name="chef_img"
                    accept="image/jpeg,image/png,image/webp"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <!-- Gallery Images -->
        <?php if ($isEdit): ?>
            <section aria-labelledby="gallery-heading" class="bg-white border rounded-lg p-6 mb-6">
                <h2 id="gallery-heading" class="text-xl font-bold mb-1 border-b pb-2">Gallery Images</h2>
                <p class="text-sm text-gray-500 mb-4">These photos are the dishes for each restaurant that are on display</p>
                <?php if ($restaurant->gallery && !empty($restaurant->gallery->media_items)): ?>
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        Current Gallery (<?= count($restaurant->gallery->media_items) ?>
                        image<?= count($restaurant->gallery->media_items) !== 1 ? 's' : '' ?>):
                    </p>
                    <ul class="mb-5 grid list-none grid-cols-1 gap-4 p-0 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <?php foreach ($restaurant->gallery->media_items as $Index => $media): ?>
                            <?php
                            $imgPath  = $media?->file_path ?? '';
                            if ($imgPath && !str_starts_with($imgPath, '/')) {
                                $imgPath = '/' . $imgPath;
                            }
                            $imgAlt   = $media?->alt_text ?? ($restaurant->name . ' gallery image');
                            $mediaId  = $media->media_id ?? null;
                            $position = $Index + 1;
                            $total    = count($restaurant->gallery->media_items);
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
                                                formaction="/cms/artists/gallery-remove/<?= (int)$restaurant->restaurant_id ?>/<?= (int)$mediaId ?>"
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

        <!-- Website -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Website</h2>
            <input type="url"
                name="website_url"
                value="<?= htmlspecialchars($restaurant->website_url ?? '') ?>"
                class="w-full px-4 py-2 border rounded-lg"
                placeholder="https://restaurantwebsite.com">
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                <?= $isEdit ? 'Update Restaurant' : 'Create Restaurant' ?>
            </button>
            <a href="/cms/restaurants"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold">
                Cancel
            </a>
        </div>
    </form>
</section>

<script>
    tinymce.init({
        selector: '.tinymce',
        menubar: false,
        license: 'gpl',
        plugins: 'autoresize link lists image',
        toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
        min_height: 120,
        max_height: 400,
        branding: false,
        promotion: false,
        resize: true,
        autoresize_bottom_margin: 20,

        images_upload_url: '/cms/media/upload-tinymce',
        automatic_uploads: true,

        images_upload_handler: function(blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());
            formData.append('alt_text', 'Content image');
            formData.append('category', 'Restaurants');

            fetch('/cms/media/upload-tinymce', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        success(data.file_path);
                    } else {
                        failure(data.error || 'Upload failed');
                    }
                })
                .catch(err => {
                    failure('Upload failed: ' + err.message);
                });
        }
    });
    document.querySelectorAll('.cuisine-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const checked = document.querySelectorAll('.cuisine-checkbox:checked');
            if (checked.length > 3) {
                cd.checked = false;
                alert('you can select max 3 cuisines');
            }
        })
    })
</script>