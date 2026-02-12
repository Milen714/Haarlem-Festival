<?php
namespace App\Views\Cms;
$existingHtml = '<article class="flex flex-row w-full justify-around items-center">

            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-history-accent] shadow-md"
                src="/Assets/Home/HistoryEventHome.png" alt="St. Bavo Church">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Family Fun Day</h1>
                    </header>
                    <p>Join us for a day filled with exciting activities for all ages, including games, crafts, and live
                        entertainment. Perfect for families looking to create lasting memories together.</p>
                </div>
                <a class="home_history_button" href="hasd">View Event Details</a>
            </div>
        </article>';
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
    toolbar: 'undo redo | bold italic | h1 h2 | bullist numlist | link | image',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2',
    branding: false
});
</script>