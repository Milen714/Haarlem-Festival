<?php

namespace App\Views\Cms;

use App\CmsModels\Page;

/** @var Page $pageData */
$pageData = $pageData ?? null;
?>

<section class="flex flex-col gap-4 items-start justify-center p-5 mx-auto mb-10">
    <div>
        <header class="headers_home pb-5">
            <h1 class="text-4xl font-bold">Edit Home Page</h1>
        </header>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        ✓ <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        ✗ <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <!-- IMPORTANT: Added enctype for file uploads -->
    <form method="POST" action="/home-update" enctype="multipart/form-data"
        class="flex flex-col gap-4 items-start justify-center w-full">

        <input type="hidden" name="page_id" value="<?= htmlspecialchars($pageData->page_id) ?>">
        <input type="hidden" name="page_type" value="<?= htmlspecialchars($pageData->page_type->value) ?>">

        <section class="border p-4 rounded-md mb-4 inset-shadow-sm bg-white">
            <article class="flex flex-row gap-2 justify-between">
                <article class="input_group">
                    <label class="input_label" for="title">Page Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($pageData->title) ?>"
                        class="form_input">
                </article>
                <article class="input_group">
                    <label class="input_label" for="slug">Page Slug:</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($pageData->slug) ?>"
                        class="form_input">
                </article>
            </article>
            <article class="input_group">
                <label class="input_label" for="content">SideBar Content:</label>
                <textarea id="content" class="tinymce" name="content" rows="5"
                    cols="40"><?= htmlspecialchars($pageData->sidebar_html) ?></textarea>
            </article>
        </section>

        <?php foreach ($pageData->content_sections as $i => $section): ?>
        <?php include __DIR__ . '/Components/SectionForm.php'; ?>
        <?php endforeach; ?>

        <button class="button_primary" type="submit">💾 Save Changes</button>
    </form>
</section>

<script>
tinymce.init({
    selector: '.tinymce',
    menubar: false,
    plugins: 'autoresize link lists image',
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
    extended_valid_elements: 'button[type|class|data-accordion-trigger|aria-expanded],div[class|data-accordion-root],span[class]',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    min_height: 150,
    max_height: 700,
    branding: false,
    // Image upload for TinyMCE
    images_upload_url: '/cms/media/upload-tinymce',
    automatic_uploads: true,
    images_upload_handler: function(blobInfo, success, failure) {
        const formData = new FormData();
        formData.append('image', blobInfo.blob(), blobInfo.filename());
        formData.append('alt_text', 'Content image');
        formData.append('category', 'Home/Content');

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