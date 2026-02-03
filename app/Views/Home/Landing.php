<?php
namespace App\Views\Home;

?>

<section class="flex flex-col bg_colors_home text_colors_home pt-4">
    <p>Hello from the Home/Landin VIEW</p>
</section>

<?php
require_once __DIR__ . '/../../config/secrets.php';

echo "Environment: " . $stripeSecretKey . "<br>";

if(isset($message)){
    echo "<h2>" . htmlspecialchars($message) . "</h2>";
}