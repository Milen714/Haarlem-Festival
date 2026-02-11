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
    <form method="POST" action="/home-page-update" class=" flex flex-col gap-4 items-start justify-center">
        <article class="text-center">
            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($pageData->title) ?>"
                class="input_primary">
        </article>

        <article class="text-center">
            <label for="content">Content</label>
            <!-- TinyMCE -->
            <textarea id="content" name="content" rows="5"
                cols="80"><?= htmlspecialchars($pageData->sidebar_html) ?></textarea>
        </article>


        <button class="button_primary" type="submit">Save</button>
    </form>
</section>



<script>
tinymce.init({
    selector: '#content',
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    branding: false
});
</script>