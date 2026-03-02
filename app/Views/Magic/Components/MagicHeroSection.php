<?php 
namespace App\Views\Magic\Components;
use App\CmsModels\PageSection;
/** @var PageSection $heroSection */
?>

<section class="flex flex-col gap-5 mb-6">
    <section
        class="flex flex-col md:flex-row justify-around w-[90%] mx-auto py-10 gap-6 md:gap-10 font-courierprime border-[var(--magic-gold-accent-muted)] border-b-2 ">
        <article
            class="bg-[var(--magic-bg-secondary-dark)] h-min p-5 rounded-md shadow-xl text-courierprime w-full md:w-auto min-w-0">
            <?php echo $heroSection->content_html ?>
            <div class="pb-6 border-[var(--magic-gold-accent-muted)] border-b-2">
                <?php
                $ctaURL = $heroSection->cta_url ?? '/events-magic-tickets';
                $buttonLabel = $heroSection->cta_text ?? 'Get Tickets';
                include 'RedButton.php'; ?>
            </div>
        </article>
        <img class="h-[85vh] max-w-full w-full md:w-auto object-contain"
            src="<?php echo htmlspecialchars($heroSection->media->file_path) ?>"
            alt="<?php echo htmlspecialchars($heroSection->media->alt_text) ?>">
    </section>
    <?php echo $heroSection->content_html_2 ?>
</section>