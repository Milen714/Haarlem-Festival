<?php
namespace App\Views\ShoppingCart\Components;
?>

<div class="flex flex-col items-center justify-center w-[90%] mx-auto">
    <div id="payment-spinner" class="flex flex-col items-center justify-center" role="status" aria-live="polite">
        <div class="loader"></div>
        <p class="text-gray-600 mt-2">Loading payment details...</p>
    </div>
</div>
