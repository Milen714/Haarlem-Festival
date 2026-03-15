<?php
namespace App\Views\Magic;
use App\ViewModels\Magic\MagicAccessibility;
/** @var MagicAccessibility $pageModel */
$heroSection = $pageModel->heroSection;
?>

<section
    class="flex flex-col gap-6 bg_colors_home text-white pt-4 bg-[var(--magic-bg-primary)] w-full overflow-x-hidden">
    <?php
        if ($pageModel->heroSection): 
            include 'Components/MagicHeroSection.php'; ?>
    <?php endif ?>

    <section class="w-[90%] mx-auto">
        <?php include 'Components/MagicNav.php'; ?>
    </section>

    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php 
        $pageModel->displaySections($pageModel->introSections); ?>
    </section>

    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php if ($pageModel->phoneDemoSections): ?>
        <?php $pageModel->displaySections($pageModel->phoneDemoSections); ?>
        <?php endif; ?>
    </section>
    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php 
        $pageModel->displaySections($pageModel->secretFileSections); ?>
    </section>

    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-alternative)]">
        <section class="flex flex-col lg:flex-row justify-center  gap-8 w-full">

            <?php if ($pageModel->lorentzPromoSections): ?>
            <?php $pageModel->displaySections($pageModel->lorentzPromoSections); ?>
            <?php endif; ?>

            <!-- <section class="w-1/2 ">
                <?php if ($closingLorentzPair['article']): ?>
                    <?php $section = $closingLorentzPair['article']; ?>
                    <?php include 'Components/MagicParagraph.php'; ?>
                    <?php endif; ?>
                </section> -->

        </section>
        <!-- <?php include 'Components/LorentsScheduleRowsContainer.php'; ?> -->

        <?php if ($pageModel->lorentzScheduleContainer): ?>
        <?php $pageModel->displaySections($pageModel->lorentzScheduleContainer); ?>
        <?php endif; ?>

    </section>
    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">

        <header class="text-left mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
            <strong>
                <h2>Practical Info & Map</h2>
            </strong>
        </header>
        <img src="/Assets/Magic/Map-Games.svg" alt="">

    </section>


</section>