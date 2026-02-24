<?php /** @var App\CmsModels\Page $pageData */ ?>
<div class="bg-[#fefbf0] min-h-screen pb-16">
    
    <?php include __DIR__ . '/Components/TourHero.php'; ?>
    
    <div class="container mx-auto max-w-[1100px] px-4">
        
        <?php include __DIR__ . '/Components/TourBreadcrumb.php'; ?>

        <?php include __DIR__ . '/Components/TourInfo.php'; ?>
        
        <?php include __DIR__ . '/Components/TourSeparator.php'; ?>

        <?php include __DIR__ . '/Components/TourCta.php'; ?>

        <?php include __DIR__ . '/Components/TourSeparator.php'; ?>

        <?php include __DIR__ . '/Components/TourTickets.php'; ?>

    </div>
</div>