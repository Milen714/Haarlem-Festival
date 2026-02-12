<?php 
namespace App\Views\Cms;
use App\CmsModels\TheFestivalPage;
/** @var TheFestivalPage $pageData */
$pageData = $pageData ?? null;

?>

<section class="flex flex-col gap-4 items-start justify-center p-5  mx-auto mb-10">
    <div>
        <header class="headers_home pb-5">
            <h1 class="text-4xl font-bold">Edit Home Page</h1>
        </header>
    </div>
    <form method="POST" action="/home-update" class=" flex flex-col gap-4 items-start justify-center ">
        <input type="hidden" name="page_id" value="<?= htmlspecialchars($pageData->page_id) ?>">
        <input type="hidden" name="page_type" value="<?= htmlspecialchars($pageData->page_type->value) ?>">
        <section class="border p-4 rounded-md mb-4 inset-shadow-sm">
            <article class="flex flex-row gap-2 justify-between">
                <article class="input_group ">
                    <label class="input_label" for="title">Page Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($pageData->title) ?>"
                        class="form_input">
                </article>
                <article class="input_group ">
                    <label class="input_label" for="slug">Page Slug:</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($pageData->slug) ?>"
                        class="form_input">
                </article>
            </article>
            <article class="input_group ">
                <label class="input_label" for="content">SideBar Content:</label>
                <!-- TinyMCE -->
                <textarea id="content" class="tinymce" name="content" rows="5"
                    cols="40"><?= htmlspecialchars($pageData->sidebar_html) ?></textarea>
            </article>
        </section>
        <?php foreach ($pageData->content_sections as $i => $section): ?>
        <?php include __DIR__ . '/Components/SectionForm.php'; ?>
        <?php endforeach; ?>

        <button class="button_primary" type="submit">Save</button>
    </form>
</section>



<script>
tinymce.init({
    selector: '.tinymce',
    menubar: false,
    plugins: 'autoresize link lists',
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    min_height: 150,
    max_height: 700,
    branding: false
});
</script>