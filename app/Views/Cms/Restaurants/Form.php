<?php
namespace App\Views\Cms\Restaurants;

use App\Models\Restaurant;
use App\Models\Venue;

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
</script>