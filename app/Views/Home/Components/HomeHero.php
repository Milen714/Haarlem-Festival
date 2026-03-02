<?php
namespace App\Views\Home\Components;
?>


<div class="w-[90%] mx-auto flex flex-col md:block overflow-x-hidden">
    <header class="mb-5 border-b-4 border-[--home-gold-accent] pb-2">
        <h1 class="text-6xl text-[var(--text-home-primary)] font-bold text-center mb-4">
            <?php echo htmlspecialchars($heroSection->title) ?></h1>
    </header>
    <div class="flex flex-col md:flex-row justify-evenly">
        <div class="w-full md:w-[45%] min-w-0">
            <img src="<?php echo htmlspecialchars($heroSection->media->file_path) ?>"
                alt="<?php echo htmlspecialchars($heroSection->media->alt_text) ?>"
                class="w-full max-w-full h-auto object-cover rounded-md md:rounded-l-3xl">
        </div>
        <div class="w-full md:w-[45%] min-w-0 flex flex-col justify-center text-lg space-y-4 text-black">
            <?php echo $heroSection->content_html ?>
        </div>
    </div>
</div>