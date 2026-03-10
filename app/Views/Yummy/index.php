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
            $havenSection = $section; // Store the haven section for later use
            include 'Components/YummyHaven.php';
        } 
        
        elseif (strpos($componentTitle, 'join') !== false) {
                $joinSection = $section; // Store the join section for later use
            include 'Components/YummyJoinFes.php';
        } 

        elseif (strpos($componentTitle, 'featured cuisine') !== false) {
            $featuredCuisineSection = $section; // Store the featured cuisine section for later use
            include 'Components/YummyFeaturedCuisine.php';
        } 

        elseif (strpos($componentTitle, 'discover haarlem') !== false) {
            $discoverSection = $section; // Store the discover section for later use
            include 'Components/YummyDiscoverHaarlem.php';
        } 

        elseif (strpos($componentTitle, 'explore') !== false) {
            $exploreSection = $section; // Store the explore section for later use
            include 'Components/YummyExploreRestaurants.php';

            include 'Components/YummyEventsSection.php';
        } 
        
        else {
            include 'Components/YummyEventsSection.php';
        }
    ?>

<?php endforeach; ?>

