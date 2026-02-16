<?php
namespace App\Views\Home\Yummy;
// echo isset($id) ? "ID is set: " . $id : "ID is not set";


?>

<section class="flex flex-col gap-6 bg-[var(--yummy-primary)] pt-4">
    <?php include 'Components/YummyHero.php'; ?>
    <?php include 'Components/YummyHaven.php'; ?>
    <?php include 'Components/YummyJoinFes.php'; ?>
    <?php include 'Components/YummyFeaturedCuisine.php'; ?>
    <?php include 'Components/YummyDiscoverHaarlem.php'; ?>
    <?php include 'Components/YummyExploreRestaurants.php'; ?>
    <?php include 'Components/YummyEventsSection.php'; ?>
    
</section>