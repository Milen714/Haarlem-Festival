<?php
namespace App\Views\Magic;
use App\CmsModels\PageSection;
use App\ViewModels\Magic\MagicLanding;
use App\CmsModels\Enums\SectionType;
/** @var MagicLanding $pageModel */
$heroSection = $pageModel->heroSection;
$closingLorentzPair = $pageModel->getClosingLorentzPair();
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
        $pageModel->displayCharacterSections($pageModel->characterSections); ?>
    </section>

    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php if ($pageModel->stampBookSection): ?>
        <?php $pageModel->displayImageSection($pageModel->stampBookSection); ?>
        <?php endif; ?>
    </section>
    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php 
        $pageModel->displayGameSections($pageModel->gameSections); ?>
    </section>

    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-alternative)]">
        <section class="flex flex-col lg:flex-row gap-8 items-start w-[95%] mx-auto">
            <section class="">
                <?php if ($closingLorentzPair['image']): ?>
                <?php $pageModel->displayImageSection($closingLorentzPair['image']); ?>
                <?php endif; ?>
            </section>

            <section class=" ">
                <?php if ($closingLorentzPair['article']): ?>
                <?php $section = $closingLorentzPair['article']; ?>
                <?php include 'Components/MagicParagraph.php'; ?>
                <?php endif; ?>
            </section>

            <section class="w-full flex flex-col gap-3">
                <?php include 'Components/LorentzScheduleCard.php'; ?>
                <?php include 'Components/LorentzScheduleCard.php'; ?>
                <?php include 'Components/LorentzScheduleCard.php'; ?>
            </section>
        </section>
    </section>
    <section
        class="flex flex-col justify-center items-center w-[90%] mx-auto magic-border py-5 bg-[var(--magic-bg-secondary-dark)]">
        <?php include 'Components/MagicAccordion.php'; ?>
        <?php include 'Components/MagicAccordion.php'; ?>
        <?php include 'Components/MagicAccordion.php'; ?>
    </section>


</section>