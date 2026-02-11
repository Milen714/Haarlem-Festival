<?php
namespace App\Views\Home\Yummy;
echo isset($id) ? "ID is set: " . $id : "ID is not set";


?>

<section class="flex flex-col gap-6 bg_colors_home text_colors_home pt-4">
    <?php include 'Components/YummyHero.php'; ?>
    <?php include 'Components/YummyDiscoverHaarlem.php'; ?>
    <?php include 'Components/YummyExploreRestaurants.php'; ?>
    <?php include 'Components/YummyEventsSection.php'; ?>
    <?php include 'Components/YummyJoinFes.php'; ?>
    <?php include 'Components/YummyHaven.php'; ?>

</section>