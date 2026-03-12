<?php
namespace App\Views\ShoppingCart\Components;
$dotstyles = [
    'bg-active' => 'bg-[var(--text-home-primary)] dark:bg-[var(--text-home-high-contrast-primary)] text-white',
    'bg-inactive' => 'bg-[var(--payment-flow-gray-muted)] dark:bg-[var(--payment-flow-gray-muted)] text-gray-500 dark:text-gray-400',
];
?>


<div class="flex flex-row items-start w-full mt-5">

    <!-- Vertical line (mobile) -->


    <!-- Horizontal line (desktop) -->
    <!-- <div class="absolute top-[29%] left-0 right-0 h-2 bg-black/70 dark:bg-white/70 hidden md:block"></div> -->

    <!-- STEP 1 -->
    <?php 
        $stepNumber = 1;
        $isCompleted = true;
        include __DIR__ . '/CheckoutStep.php'; 
    ?>


    <!-- STEP 2 -->
    <?php 
        $stepNumber = 2;
        $isCompleted = false;
        include __DIR__ . '/CheckoutStep.php'; 
    ?>

    <!-- STEP 3 -->
    <?php 
        $stepNumber = 3;
        $isCompleted = false;
        include __DIR__ . '/CheckoutStep.php'; 
    ?>
    <!-- STEP 4 -->
    <?php 
        $stepNumber = 4;
        $isCompleted = false;
        include __DIR__ . '/CheckoutStep.php'; 
    ?>

</div>