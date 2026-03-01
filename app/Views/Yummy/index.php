<?php
namespace App\Views\Yummy;
// echo isset($id) ? "ID is set: " . $id : "ID is not set";

?>

<?php foreach ($sections as $section): ?>
    <?php
       
        $componentTitle = strtolower($section->title);

        if (strpos($componentTitle, 'yummy!') !== false) {
            $heroSection = $section; // Store the hero section for later use
            include 'Components/YummyHero.php';
        }

        elseif (strpos($componentTitle, 'haven') !== false) {
            include 'Components/YummyHaven.php';
        } 
        
        elseif (strpos($componentTitle, 'join') !== false) {
            include 'Components/YummyJoinFes.php';
        } 

        elseif (strpos($componentTitle, 'featured cuisine') !== false) {
            include 'Components/YummyFeaturedCuisine.php';
        } 

        elseif (strpos($componentTitle, 'discover haarlem') !== false) {
            include 'Components/YummyDiscoverHaarlem.php';
        } 

        elseif (strpos($componentTitle, 'explore') !== false) {
            include 'Components/YummyExploreRestaurants.php';
        } 
        
        else {
            include 'Components/YummyEventsSection.php';
        }
    ?>

<?php endforeach; ?>