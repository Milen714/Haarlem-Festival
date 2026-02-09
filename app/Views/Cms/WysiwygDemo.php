<?php
namespace App\Views\Cms;
$existingHtml = '<h1><strong>Hello</strong></h1><p>This is saved content.</p>';
?>


<form method="POST" action="/wysiwyg-demo-post" class="w-[90%] mx-auto mt-10">
    <label for="content">Content</label><br>

    <!-- TinyMCE replaces this textarea -->
    <textarea id="content" name="content" rows="15" cols="80"><?= htmlspecialchars($existingHtml) ?></textarea>

    <br><br>
    <button type="submit">Save</button>
</form>

<script>
tinymce.init({
    selector: '#content',
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    branding: false
});
</script>