<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;

?>

<section class="flex flex-col md:flex-row gap-8 w-[95%] mx-auto mt-5">
    <section class="">
        <h2 class="text-2xl font-bold mb-4">Order Details</h2>
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
    </section>
    <?php
                include __DIR__ . '/Components/OrderSummaryRows.php';
            ?>
</section>