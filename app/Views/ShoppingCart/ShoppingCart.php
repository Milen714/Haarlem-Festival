<?php 
namespace App\Views\ShoppingCart;

use App\Models\Enums\EventType;
use App\Models\TicketType;
use App\Models\Schedule;
use App\Models\Payment\Order;

/** @var TicketType $ticketType */
$ticketType = isset($ticketType) ? $ticketType : null;

/** @var Schedule $scheduleItem */
$scheduleItem = new Schedule();

/** @var Order|null $order */
$order = isset($vars['order']) ? $vars['order'] : null;

$summary = [
    'jazz' => ['label' => 'Haarlem Jazz', 'unit' => 'Tickets', 'count' => 0, 'amount' => 0.0],
    'yummy' => ['label' => 'Yummy!', 'unit' => 'Reservations', 'count' => 0, 'amount' => 0.0],
    'dance' => ['label' => 'Dance!', 'unit' => 'Day Pass', 'count' => 0, 'amount' => 0.0],
];

$subtotal = 0.0;

if ($order && is_array($order->orderItems)) {
    foreach ($order->orderItems as $item) {
        $quantity = (int)($item->quantity ?? 0);
        $unitPrice = isset($item->unit_price) ? (float)$item->unit_price : (float)($item->ticket_type->price ?? 0.0);
        $lineAmount = $quantity * $unitPrice;
        $subtotal += $lineAmount;

        $eventType = $item->ticket_type?->schedule?->event_category?->type ?? null;
        if ($eventType === EventType::Jazz) {
            $summary['jazz']['count'] += $quantity;
            $summary['jazz']['amount'] += $lineAmount;
        } elseif ($eventType === EventType::Yummy) {
            $summary['yummy']['count'] += $quantity;
            $summary['yummy']['amount'] += $lineAmount;
        } elseif ($eventType === EventType::Dance) {
            $summary['dance']['count'] += $quantity;
            $summary['dance']['amount'] += $lineAmount;
        }
    }
}

$serviceFee = round($subtotal * 0.025, 2);
$total = $subtotal + $serviceFee;
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

        <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 w-full md:w-[25%]">
            <h3 class="font-bold text-lg border-b-2 border-[#1e4b6e] pb-2 mb-6 w-fit">Order Summary</h3>

            <dl class="space-y-4 text-xs font-medium text-gray-600">
                <div class="flex justify-between">
                    <dt><?= $summary['jazz']['label'] ?> (<?= $summary['jazz']['count'] ?>
                        <?= $summary['jazz']['unit'] ?>)</dt>
                    <dd class="font-bold text-black">€<?= number_format($summary['jazz']['amount'], 2) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt><?= $summary['yummy']['label'] ?> (<?= $summary['yummy']['count'] ?>
                        <?= $summary['yummy']['unit'] ?>)</dt>
                    <dd class="font-bold text-black">€<?= number_format($summary['yummy']['amount'], 2) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt><?= $summary['dance']['label'] ?> (<?= $summary['dance']['count'] ?>
                        <?= $summary['dance']['unit'] ?>)</dt>
                    <dd class="font-bold text-black">€<?= number_format($summary['dance']['amount'], 2) ?></dd>
                </div>
                <hr class="border-gray-100 my-4">
                <div class="flex justify-between">
                    <dt>Subtotal</dt>
                    <dd class="font-bold text-black">€<?= number_format($subtotal, 2) ?></dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="flex items-center">Service Fee (2.5%) <span
                            class="ml-1 text-blue-500 cursor-help">ⓘ</span></dt>
                    <dd class="font-bold text-black">€<?= number_format($serviceFee, 2) ?></dd>
                </div>
            </dl>

            <div class="mt-8 flex justify-between items-end">
                <span class="text-2xl font-bold text-gray-800">Total</span>
                <span class="text-3xl font-bold text-black">€<?= number_format($total, 2) ?></span>
            </div>
        </section>


    </section>

</section>