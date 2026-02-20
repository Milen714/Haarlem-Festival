<?php
namespace App\Views\Magic;
/** @var Page $pageData */
?>

<section
    class="flex flex-col gap-6 bg_colors_home text-white pt-4 bg-[var(--magic-bg-primary)] w-full overflow-x-hidden">
    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php if ($pageData): ?>
        <?php $section = $pageData->content_sections[0];
            include 'Components/MagicAccordion.php'; ?>
        <?php endif; ?>

    </section>

</section>