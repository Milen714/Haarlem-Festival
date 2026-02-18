<?php

namespace App\Views\Cms;

use App\CmsModels\Page;

/** @var Page $pageData */
$pageData = $pageData ?? null;
$pageTitle = $pageTitle ?? 'Edit Page';
$submitUrl = $submitUrl ?? '/cms/page/update';
$backUrl = $backUrl ?? '/cms';
$uploadCategory = $uploadCategory ?? 'Home/Content';
$eventType = $eventType ?? null;
?>

<section class="flex flex-col gap-4 items-start justify-center p-5 mx-auto mb-10 max-w-6xl">
    <div class="w-full">
        <header class="headers_home pb-5 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="text-sm text-gray-600 mt-1">Manage content and media for this page</p>
            </div>
            <a href="<?= htmlspecialchars($backUrl) ?>" class="button_secondary">← Back</a>
        </header>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="w-full bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="w-full bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($submitUrl) ?>" enctype="multipart/form-data"
        class="flex flex-col gap-4 items-start justify-center w-full">

        <input type="hidden" name="page_id" value="<?= htmlspecialchars($pageData->page_id) ?>">
        <input type="hidden" name="page_type" value="<?= htmlspecialchars($pageData->page_type->value) ?>">
        <?php if ($eventType): ?>
        <input type="hidden" name="event_type" value="<?= htmlspecialchars($eventType) ?>">
        <?php endif; ?>

        <!-- Page Details -->
        <section class="border p-4 rounded-md mb-4 inset-shadow-sm w-full bg-white">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Page Details</h2>

            <article class="flex flex-row gap-2 justify-between">
                <article class="input_group flex-1">
                    <label class="input_label" for="title">Page Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($pageData->title) ?>"
                        class="form_input" required>
                </article>
                <article class="input_group flex-1">
                    <label class="input_label" for="slug">Page Slug:</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($pageData->slug) ?>"
                        class="form_input" required>
                    <p class="text-xs text-gray-500 mt-1">URL identifier (e.g., "jazz-festival")</p>
                </article>
            </article>

            <?php if (isset($pageData->sidebar_html) && $pageData->sidebar_html !== null): ?>
            <article class="input_group mt-4">
                <label class="input_label" for="content">Sidebar Content:</label>
                <textarea id="content" class="tinymce" name="content"
                    rows="5"><?= htmlspecialchars($pageData->sidebar_html) ?></textarea>
            </article>
            <?php endif; ?>
        </section>

        <!-- Page Sections -->
        <section class="w-full">
            <h2 class="text-xl font-semibold mb-4">Page Sections (<?= count($pageData->content_sections) ?>)</h2>

            <?php if (!empty($pageData->content_sections)): ?>
            <?php foreach ($pageData->content_sections as $i => $section): ?>
            <?php include __DIR__ . '/Components/SectionForm.php'; ?>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="bg-gray-100 border border-gray-300 rounded p-6 text-center">
                <p class="text-gray-600">No sections available for this page.</p>
            </div>
            <?php endif; ?>
        </section>

        <!-- Submit Button -->
        <div class="w-full flex gap-4 border-t pt-4">
            <button class="button_primary" type="submit">💾 Save Changes</button>
            <a href="<?= htmlspecialchars($backUrl) ?>" class="button_secondary">Cancel</a>
        </div>
    </form>
</section>

<!-- TinyMCE Configuration -->
<script>
tinymce.init({
    selector: '.tinymce',
    menubar: false,
    plugins: 'autoresize link lists image',
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    min_height: 100,
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
        formData.append('category', '<?= $uploadCategory ?>');

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