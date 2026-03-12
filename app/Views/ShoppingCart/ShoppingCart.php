<?php 
namespace App\Views\ShoppingCart;

use App\Models\TicketType;
use App\Models\Schedule;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

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
        
    ?>
    <section class="flex flex-col md:flex-row gap-5">
        <section class="flex flex-col gap-2 w-full md:w-[75%]">
            <?php if ($order && is_array($order->orderItems) && !empty($order->orderItems)): ?>
            <?php
                    foreach ($order->orderItems as $item) {
                        $scheduleItem = $item->ticket_type->schedule;
                        $ticketType = $item->ticket_type;
                        include __DIR__ . '/Components/TicketItemRow.php';
                    }
                ?>
            <?php else: ?>
            <p class="text-gray-600">Your shopping cart is empty.</p>
            <?php endif; ?>
            <a href="/checkout">Test Checkout</a>
        </section>

        <?php 
            include __DIR__ . '/Components/OrderSummaryPartial.php';
        ?>


    </section>

</section>