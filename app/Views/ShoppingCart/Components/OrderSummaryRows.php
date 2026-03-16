<?php
namespace App\Views\ShoppingCart\Components;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
/** @var ShoppingCartViewModel|null $viewModel */


?>

<section class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col gap-2">
    <h3 class="font-bold text-lg border-b-2 border-[#1e4b6e] pb-2 mb-6 w-fit">Order Summary</h3>

    <?php
        foreach ($viewModel?->order->orderItems ?? [] as $item) {
                        $scheduleItem = $item->ticket_type->schedule;
                        $ticketType = $item->ticket_type;
                        include __DIR__ . '/TicketItemRow.php';
                }
    ?>
    <dl class="space-y-4 text-xs font-medium text-gray-600 flex-grow">
        <div class="flex justify-between">
            <dt>Tickets/Reservations (<?= (int)($viewModel?->nCartItems ?? 0) ?>)</dt>
            <dd class="font-bold text-black">€<?= number_format((float)($viewModel?->subtotal ?? 0.0), 2) ?></dd>
        </div>
        <?php if (($viewModel?->reservationFees ?? 0.0) > 0): ?>
        <div class="flex justify-between">
            <dt>Reservation Fees</dt>
            <dd class="font-bold text-black">€<?= number_format((float)$viewModel->reservationFees, 2) ?></dd>
        </div>
        <?php endif; ?>
        <hr class="border-gray-100 my-4">
        <div class="flex justify-between">
            <dt>Subtotal</dt>
            <dd class="font-bold text-black">€<?= number_format((float)($viewModel?->subtotal ?? 0.0), 2) ?></dd>
        </div>
        <div class="flex justify-between items-center">
            <dt class="flex items-center">Service Fee (2.5%) <span class="ml-1 text-blue-500 cursor-help">ⓘ</span></dt>
            <dd class="font-bold text-black">€<?= number_format((float)($viewModel?->serviceFee ?? 0.0), 2) ?></dd>
        </div>
    </dl>

    <div class="mt-8 mb-3 flex justify-between items-end">
        <span class="text-xl lg:text-2xl font-bold text-gray-800">Total</span>
        <span
            class="text-2xl lg:text-3xl font-bold text-black">€<?= number_format((float)($viewModel?->total ?? 0.0), 2) ?></span>
    </div>
    <?php if ($showProceedButton): ?>
    <a href="/checkout" class="home_dance_button mt-auto block text-center">Proceed to Checkout</a>
    <?php endif; ?>
</section>