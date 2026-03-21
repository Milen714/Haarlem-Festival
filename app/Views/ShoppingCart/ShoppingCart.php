<?php 
namespace App\Views\ShoppingCart;

use App\Models\TicketType;
use App\Models\Schedule;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

use function App\Views\ShoppingCart\Components\displaySteps;

/** @var TicketType $ticketType */
$ticketType = isset($ticketType) ? $ticketType : null;

/** @var Schedule $scheduleItem */
$scheduleItem = new Schedule();

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;
?>


<section class="flex flex-col gap-6 font-roboto w-[95%] mx-auto  mt-5">
    <?php 
            include __DIR__ . '/Components/CheckoutProgress.php';
            displaySteps(1); 
        
    ?>
    <section class="flex flex-col md:flex-row gap-3">
        <section class="flex flex-col gap-2 w-full md:w-[75%] bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <?php if ($order && is_array($order->orderItems) && !empty($order->orderItems)): ?>
            <?php
                    foreach ($order->orderItems as $item) {
                        $scheduleItem = $item->ticket_type->schedule;
                        $ticketType = $item->ticket_type;
                        $showCrudButtons = true;
                        include __DIR__ . '/Components/TicketItemRow.php';
                    }
                ?>
            <?php else: ?>
            <p class="text-gray-600">Your shopping cart is empty.</p>
            <?php endif; ?>

        </section>

        <?php 
            include __DIR__ . '/Components/OrderSummaryPartial.php';
        ?>


    </section>

</section>

<section id="overlay-container"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div id="overlay">
        <h1>Delete Item</h1>
    </div>
</section>

<script src="/Js/UpdateCart.js"></script>