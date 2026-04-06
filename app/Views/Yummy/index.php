<?php
namespace App\Views\Yummy;
/** @var \App\ViewModels\Yummy\YummyHomeViewModel $viewModel */
// echo isset($id) ? "ID is set: " . $id : "ID is not set";
$contentData = [
    [
        'title' => 'Jazz - Haarlem Festival',
        'description' => 'Experience the best of Haarlem’s culinary scene with our curated selection of restaurants, each offering unique flavors and dining experiences.',
        'file_path' => 'Assets/Jazz/JazzHome/Haarlem-Homepage.webp',
        'alt' => 'Culinary Delights'
    ],
    [
        'title' => 'Magic - Haarlem Festival',
        'description' => 'Experience the best of Haarlem’s culinary scene with our curated selection of restaurants, each offering unique flavors and dining experiences.',
        'file_path' => 'Assets/Magic/AltHeroImage.png',
        'alt' => 'Magic Festival'
    ],
    [
        'title' => 'History - Haarlem Festival',
        'description' => 'Experience the best of Haarlem’s culinary scene with our curated selection of restaurants, each offering unique flavors and dining experiences.',
        'file_path' => 'Assets/History/History_Church_1.png',
        'alt' => 'History Festival'
    ],
    [
        'title' => 'Dance - Haarlem Festival',
        'description' => 'Experience the best of Haarlem’s culinary scene with our curated selection of restaurants, each offering unique flavors and dining experiences.',
        'file_path' => 'Assets/Dance/DanceHome/69a41a0ad1451_1772362250.webp',
        'alt' => 'Dance Festival'
    ],
    
];

?>

<?php foreach ($viewModel->sections as $section): ?>
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

            $events = $viewModel->events;
            include 'Components/YummyEventsSection.php';
        } 
        
        else {
            $events = $viewModel->events;
            include 'Components/YummyEventsSection.php';
        }
    ?>

<?php endforeach; ?>