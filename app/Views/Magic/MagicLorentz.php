<?php 
namespace App\Views\Magic;
use App\ViewModels\Magic\MagicAccessibility;
/** @var MagicAccessibility $pageModel */
$heroSection = $pageModel->heroSection;
?>

<section class="flex flex-col gap-6 bg_colors_home text-white pt-4 bg-[var(--magic-bg-primary)]  overflow-x-hidden">

    <section class="w-[90%] mx-auto">
        <?php
        if ($pageModel->heroSection): 
            include 'Components/MagicAltHero.php';
            ?>
        <?php endif ?>
    </section>

    <section class="w-[90%] mx-auto">
        <?php include 'Components/MagicNav.php'; ?>
    </section>

    <section
        class="flex flex-col gap-6 justify-center mb-10 items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">

        <?php
        if ($pageModel->introSections) {
            $pageModel->displaySections($pageModel->introSections);
        }
        ?>
        <?php
        if ($pageModel->accessibilitySections) {
            $pageModel->displaySections($pageModel->accessibilitySections);
        }
        ?>


    </section>
</section>