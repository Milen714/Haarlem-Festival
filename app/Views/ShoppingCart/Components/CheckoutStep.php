<?php
namespace App\Views\ShoppingCart\Components;

$step = isset($stepNumber) ? $stepNumber : 1; // Default to step 1 if not set
$stepTexts = [
    1 => 'Summary',
    2 => 'Details',
    3 => 'Payment',
    4 => 'Confirmation'
];

$stepText = isset($stepTexts[$step]) ? $stepTexts[$step] : 'Step';

$dotstyles = [
    'bg-completed' => 'bg-[var(--payment-flow-green)]  text-white',
    'bg-active' => 'bg-[var(--text-home-primary)]  text-white',
    'bg-not-completed' => 'bg-[var(--payment-flow-gray-muted)] dark:bg-[var(--payment-flow-gray-muted)] text-gray-500 dark:text-gray-400',
];

$isCurrentActive = isset($isActive) && $isActive;

if (isset($isCompleted) && $isCompleted) {
    $dotStyleClass = $dotstyles['bg-completed'];
} elseif ($isCurrentActive) {
    $dotStyleClass = $dotstyles['bg-active'];
} else {
    $dotStyleClass = $dotstyles['bg-not-completed'];
}

$lineStyleClass = (isset($isCompleted) && $isCompleted && !$isCurrentActive)
    ? 'bg-[var(--payment-flow-green)]'
    : 'bg-[var(--payment-flow-gray-muted)]';
$hideLineClass = $step === 4 ? 'hidden' : '';
?>


<div class="relative flex flex-col items-center flex-1 min-w-0">

    <!-- Dot -->
    <div class="flex items-center justify-center <?= $dotStyleClass ?>
               w-10 h-10 md:w-[75px] md:h-[75px] rounded-full
                z-10
               shrink-0">
        <span class="text-base md:text-[40px]"><?= $step ?></span>
    </div>

    <!-- Content -->
    <div class="mt-2 text-center max-w-full px-1">
        <h3 class="text-[11px] md:text-xl font-semibold text-colors mb-1 md:mb-2 truncate">
            <?= $stepText ?>
        </h3>
    </div>

    <div
        class="absolute top-5 md:top-[29%] left-[calc(50%+20px)] w-[calc(100%-40px)] md:left-[calc(50%+37px)] md:w-[calc(100%-74px)] h-1 md:h-2 <?= $lineStyleClass ?> <?= $hideLineClass ?>">
    </div>

</div>