<?php
namespace App\Views\ShoppingCart\Components;

?>
<?php
function displaySteps($currentStep) {
    echo '<div class="flex flex-row items-start w-full mt-5">';

    for ($i = 1; $i <= 4; $i++) {
        $stepNumber = $i;
        $isCompleted = $i < $currentStep;
        $isActive = $i === $currentStep;
        include __DIR__ . '/CheckoutStep.php';
    }

    echo '</div>';
}
?>