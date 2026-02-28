<?php
namespace App\Views\Home\Yummy;
// echo isset($id) ? "ID is set: " . $id : "ID is not set";


?>

<?php foreach ($sections as $section): ?>
    <?php 
        $componentTitle = strtolower($section->title);

        if (strpos($componentTitle, 'yummy!') !== false) {
            $heroSection = $section; // Store the hero section for later use
            include 'Home/Yummy/Components/Yummyhero';
        }

        elseif (strpos($componentTitle, 'haven') !== false) {
            include 'Home/Yummy/Components/YummyHaven';
        } 
        
        elseif (strpos($componentTitle, 'join') !== false) {
            include 'Home/Yummy/Components/YummyJoinFes';
        } 

        elseif (strpos($componentTitle, 'featured cuisine') !== false) {
            include 'Home/Yummy/Components/YummyFeaturedCuisine';
        } 

        elseif (strpos($componentTitle, 'discover haarlem') !== false) {
            include 'Home/Yummy/Components/YummyDiscoverHaarlem';
        } 

        elseif (strpos($componentTitle, 'explore') !== false) {
            include 'Home/Yummy/Components/YummyExploreRestaurants';
        } 
        
        else {
            include 'Home/Yummy/Components/YummyEventsSection';
        }
    ?>
<!-- 
<section class="flex flex-col gap-6 bg-[var(--yummy-primary)] pt-4">
    <?php include 'Components/YummyHero.php'; ?>
    <?php include 'Components/YummyHaven.php'; ?>
    <?php include 'Components/YummyJoinFes.php'; ?>
    <?php include 'Components/YummyFeaturedCuisine.php'; ?>
    <?php include 'Components/YummyDiscoverHaarlem.php'; ?>
    <?php include 'Components/YummyExploreRestaurants.php'; ?>
    <?php include 'Components/YummyEventsSection.php'; ?>
</section> -->
<?php endforeach; ?>